<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;

class DocumentParserService
{
    public function extract(string $filePath, string $mimeType): string
    {
        if (!\Illuminate\Support\Facades\Storage::exists($filePath)) {
            throw new \Exception("File not found in storage: " . $filePath);
        }

        // Download to a temporary file because pdfparser and phpword require local paths
        $tempPath = tempnam(sys_get_temp_dir(), 'doc_');
        file_put_contents($tempPath, \Illuminate\Support\Facades\Storage::get($filePath));

        try {
            return match(true) {
                str_contains($mimeType, 'pdf')  => $this->parsePdf($tempPath),
                str_contains($mimeType, 'word') || str_contains($mimeType, 'officedocument') => $this->parseDocx($tempPath),
                default                         => $this->parseTxt($tempPath),
            };
        } catch (\Exception $e) {
            Log::error('Error parsing document: ' . $e->getMessage());
            throw new \Exception("Could not parse document: " . $e->getMessage());
        } finally {
            // Clean up the temporary file
            @unlink($tempPath);
        }
    }

    private function parsePdf(string $path): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }

    private function parseDocx(string $path): string
    {
        $phpWord = IOFactory::load($path);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    // Handle nested elements like TextRun
                    foreach ($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $text .= $childElement->getText() . " ";
                        }
                    }
                    $text .= "\n";
                }
            }
        }
        return $text;
    }

    private function parseTxt(string $path): string
    {
        return file_get_contents($path);
    }
}
