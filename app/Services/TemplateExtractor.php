<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;

class TemplateExtractor
{
    protected PdfParser $pdfParser;

    public function __construct()
    {
        $this->pdfParser = new PdfParser();
    }

    public function extractFromFile(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            switch ($extension) {
                case 'pdf':
                    return $this->extractFromPdf($filePath);
                case 'docx':
                    return $this->extractFromDocx($filePath);
                case 'doc':
                    return $this->extractFromDoc($filePath);
                case 'txt':
                    return $this->extractFromText($filePath);
                default:
                    throw new \Exception("Unsupported file format: {$extension}");
            }
        } catch (\Exception $e) {
            Log::error("Template extraction error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function extractFromDocx(string $filePath): array
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $text = '';
            $structure = [];
            $sectionIndex = 0;

            foreach ($phpWord->getSections() as $section) {
                $sectionIndex++;
                foreach ($section->getElements() as $element) {
                    $elementType = get_class($element);
                    
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (strpos($elementType, 'TextRun') !== false) {
                        foreach ($element->getElements() as $textElement) {
                            if (method_exists($textElement, 'getText')) {
                                $text .= $textElement->getText();
                            }
                        }
                        $text .= "\n";
                    }
                }
            }

            $cleanedText = $this->cleanExtractedText($text);
            return [
                'success' => true,
                'text' => $cleanedText,
                'page_count' => $sectionIndex,
                'word_count' => str_word_count($cleanedText),
                'char_count' => mb_strlen($cleanedText),
                'metadata' => ['format' => 'docx', 'sections' => $sectionIndex],
            ];
        } catch (\Exception $e) {
            throw new \Exception("DOCX extraction failed: " . $e->getMessage());
        }
    }

    protected function extractFromPdf(string $filePath): array
    {
        try {
            $pdf = $this->pdfParser->parseFile($filePath);
            $text = $pdf->getText();
            $pages = $pdf->getPages();
            $details = $pdf->getDetails();
            $cleanedText = $this->cleanExtractedText($text);

            return [
                'success' => true,
                'text' => $cleanedText,
                'page_count' => count($pages),
                'word_count' => str_word_count($cleanedText),
                'char_count' => mb_strlen($cleanedText),
                'metadata' => ['format' => 'pdf', 'pages' => count($pages)],
            ];
        } catch (\Exception $e) {
            throw new \Exception("PDF extraction failed: " . $e->getMessage());
        }
    }

    protected function extractFromDoc(string $filePath): array
    {
        try {
            if (shell_exec('which antiword')) {
                $text = shell_exec("antiword " . escapeshellarg($filePath));
                if ($text) {
                    $cleanedText = $this->cleanExtractedText($text);
                    return [
                        'success' => true,
                        'text' => $cleanedText,
                        'page_count' => null,
                        'word_count' => str_word_count($cleanedText),
                        'char_count' => mb_strlen($cleanedText),
                        'metadata' => ['format' => 'doc'],
                    ];
                }
            }

            $phpWord = IOFactory::load($filePath, 'MsDoc');
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            $cleanedText = $this->cleanExtractedText($text);
            return [
                'success' => true,
                'text' => $cleanedText,
                'page_count' => null,
                'word_count' => str_word_count($cleanedText),
                'char_count' => mb_strlen($cleanedText),
                'metadata' => ['format' => 'doc'],
            ];
        } catch (\Exception $e) {
            throw new \Exception("DOC extraction failed: " . $e->getMessage());
        }
    }

    protected function extractFromText(string $filePath): array
    {
        try {
            $text = file_get_contents($filePath);
            $cleanedText = $this->cleanExtractedText($text);
            return [
                'success' => true,
                'text' => $cleanedText,
                'page_count' => null,
                'word_count' => str_word_count($cleanedText),
                'char_count' => mb_strlen($cleanedText),
                'metadata' => ['format' => 'txt'],
            ];
        } catch (\Exception $e) {
            throw new \Exception("Text extraction failed: " . $e->getMessage());
        }
    }

    protected function cleanExtractedText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/\.\s+/', ".\n\n", $text);
        $text = preg_replace('/[^\p{L}\p{N}\s\.\,\;\:\!\?\-\(\)\[\]\"\'\/\n]/u', '', $text);
        return trim($text);
    }

    public function validateTemplate(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return ['valid' => false, 'error' => 'File not found'];
        }

        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);
        
        if ($fileSize > 20 * 1024 * 1024) {
            return ['valid' => false, 'error' => 'File too large (max 20MB)'];
        }

        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-word',
            'text/plain',
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            return ['valid' => false, 'error' => 'Unsupported file type: ' . $mimeType];
        }

        return ['valid' => true, 'file_size' => $fileSize, 'mime_type' => $mimeType];
    }

    public function getFileInfo(string $filePath): array
    {
        $validation = $this->validateTemplate($filePath);
        if (!$validation['valid']) {
            return $validation;
        }

        return [
            'valid' => true,
            'file_name' => basename($filePath),
            'file_size' => $validation['file_size'],
            'mime_type' => $validation['mime_type'],
            'extension' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
        ];
    }
}
