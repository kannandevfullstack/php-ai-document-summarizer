<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;

class DocumentParserService
{
    public function extract(string $filePath, string $mimeType): string
    {
        $fullPath = storage_path('app/private/' . $filePath);

        // Fallback for older laravel storage path if 'private' disk doesn't exist
        if (!file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $filePath);
        }

        if (!file_exists($fullPath)) {
             throw new \Exception("File not found at path: " . $fullPath);
        }

        try {
            return match(true) {
                str_contains($mimeType, 'pdf')  => $this->parsePdf($fullPath),
                str_contains($mimeType, 'word') || str_contains($mimeType, 'officedocument') => $this->parseDocx($fullPath),
                default                         => $this->parseTxt($fullPath),
            };
        } catch (\Exception $e) {
            Log::error('Error parsing document: ' . $e->getMessage());
            throw new \Exception("Could not parse document: " . $e->getMessage());
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
