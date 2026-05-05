<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\AIService;
use App\Services\DocumentParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->paginate(10);
        
        $stats = [
            'total' => Document::count(),
            'done' => Document::where('status', 'done')->count(),
            'pending' => Document::where('status', 'pending')->count(),
            'processing' => Document::where('status', 'processing')->count(),
            'failed' => Document::where('status', 'failed')->count(),
        ];

        return view('documents.index', compact('documents', 'stats'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,docx,txt,doc|max:10240',
        ]);

        $file = $request->file('document');
        $path = $file->store('uploads'); // Stored in storage/app/private/uploads by default in L11+ or storage/app/uploads

        $document = Document::create([
            'filename'      => $file->hashName(),
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getMimeType(),
            'status'        => 'pending',
        ]);

        // Process synchronously for now (can be moved to a Queue later)
        $this->processDocument($document);

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Document uploaded and processing started!');
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);
        return view('documents.show', compact('document'));
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        // Option to delete file from storage as well
        // \Storage::delete($document->path);

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function exportPdf(Document $document)
    {
        if ($document->status !== 'done') {
            return back()->with('error', 'Cannot export. Processing not complete.');
        }

        $pdf = Pdf::loadView('documents.export.pdf', compact('document'));
        return $pdf->download($document->original_name . '_summary.pdf');
    }

    public function exportMarkdown(Document $document)
    {
        if ($document->status !== 'done') {
            return back()->with('error', 'Cannot export. Processing not complete.');
        }

        $content  = "# Summary: {$document->original_name}\n\n";
        $content .= "## Summary\n{$document->summary}\n\n";
        $content .= "## Keywords\n" . implode(', ', $document->keywords ?? []) . "\n\n";
        $content .= "## Sentiment\n{$document->sentiment} ({$document->sentiment_score})\n\n";
        $content .= "## LinkedIn Post\n{$document->linkedin_post}\n";

        return response($content, 200)
            ->header('Content-Type', 'text/markdown')
            ->header('Content-Disposition', 'attachment; filename="' . $document->original_name . '_summary.md"');
    }

    private function processDocument(Document $document): void
    {
        try {
            $document->update(['status' => 'processing']);

            // Step 1: Extract Text
            $parser = new DocumentParserService();
            $text   = $parser->extract($document->path, $document->mime_type);
            $text   = $this->cleanText($text);

            if (empty($text)) {
                throw new \Exception("Could not extract any text from the document.");
            }

            // Step 2: AI Processing
            $ai = new AIService();
            $summary   = $ai->summarizeText($text);
            $keywords  = $ai->extractKeywords($text);
            $sentiment = $ai->analyzeSentiment($text);
            $linkedin  = $ai->generateLinkedInPost($text);

            // Step 3: Save Results
            $document->update([
                'extracted_text'  => $text,
                'summary'         => $summary,
                'keywords'        => $keywords,
                'sentiment'       => $sentiment['label'] ?? 'neutral',
                'sentiment_score' => $sentiment['score'] ?? 0.5,
                'linkedin_post'   => $linkedin,
                'status'          => 'done',
            ]);

        } catch (\Exception $e) {
            $document->update(['status' => 'failed']);
            Log::error('Document processing failed [Doc ID: ' . $document->id . ']: ' . $e->getMessage());
        }
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);         // Normalize whitespace
        $text = preg_replace('/[^\x20-\x7E\n]/', '', $text); // Remove non-printable
        return trim(substr($text, 0, 40000));              // Token safety limit (~12k tokens max)
    }
}
