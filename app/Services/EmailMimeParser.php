<?php

namespace App\Services;

class EmailMimeParser
{
    /**
     * Parse raw MIME email content and extract clean parts
     *
     * @param string $rawContent
     * @return array ['html' => string|null, 'text' => string|null, 'boundary' => string|null]
     */
    public function parse(string $rawContent): array
    {
        $result = [
            'html' => null,
            'text' => null,
            'boundary' => null,
        ];

        // Detect if this is a multipart message
        if (!$this->isMultipart($rawContent)) {
            // Single part message - decode directly
            $decoded = $this->decodeContent($rawContent);
            
            // Determine if HTML or text based on content
            if ($this->looksLikeHtml($decoded)) {
                $result['html'] = $decoded;
            } else {
                $result['text'] = $decoded;
            }
            
            return $result;
        }

        // Extract boundary
        $boundary = $this->extractBoundary($rawContent);
        if (!$boundary) {
            // Fallback: try to decode as single part
            $decoded = $this->decodeContent($rawContent);
            $result['text'] = $decoded;
            return $result;
        }

        $result['boundary'] = $boundary;

        // Split by boundary
        $parts = $this->splitByBoundary($rawContent, $boundary);

        // Parse each part
        foreach ($parts as $part) {
            $partData = $this->parsePart($part);
            
            if ($partData['type'] === 'text/html' && !$result['html']) {
                $result['html'] = $partData['content'];
            } elseif ($partData['type'] === 'text/plain' && !$result['text']) {
                $result['text'] = $partData['content'];
            }
        }

        return $result;
    }

    /**
     * Extract HTML part from email
     */
    public function extractHtmlPart(string $rawContent): ?string
    {
        $parsed = $this->parse($rawContent);
        return $parsed['html'];
    }

    /**
     * Extract text part from email
     */
    public function extractTextPart(string $rawContent): ?string
    {
        $parsed = $this->parse($rawContent);
        return $parsed['text'];
    }

    /**
     * Check if content is multipart
     */
    protected function isMultipart(string $content): bool
    {
        return stripos($content, 'Content-Type: multipart/') !== false ||
               preg_match('/^--[a-zA-Z0-9]+/m', $content);
    }

    /**
     * Extract MIME boundary from content
     */
    protected function extractBoundary(string $content): ?string
    {
        // Try to extract from Content-Type header
        if (preg_match('/boundary[=:]\s*["\']?([^"\'\s;]+)["\']?/i', $content, $matches)) {
            return $matches[1];
        }

        // Try to detect boundary from content structure
        if (preg_match('/^--([a-zA-Z0-9]+)/m', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Split content by MIME boundary
     */
    protected function splitByBoundary(string $content, string $boundary): array
    {
        // Split by --boundary
        $parts = preg_split('/--' . preg_quote($boundary, '/') . '(--)?\r?\n/m', $content);
        
        // Filter out empty parts and headers-only parts
        $parts = array_filter($parts, function($part) {
            $trimmed = trim($part);
            return !empty($trimmed) && $trimmed !== '--';
        });

        return array_values($parts);
    }

    /**
     * Parse individual MIME part
     */
    protected function parsePart(string $part): array
    {
        $result = [
            'type' => 'text/plain',
            'encoding' => '7bit',
            'content' => '',
        ];

        // Split headers and body
        $parts = preg_split('/\r?\n\r?\n/', $part, 2);
        
        if (count($parts) < 2) {
            // No clear header/body separation - treat as content
            $result['content'] = $this->decodeContent($part);
            return $result;
        }

        [$headers, $body] = $parts;

        // Parse headers
        if (preg_match('/Content-Type:\s*([^;\s]+)/i', $headers, $matches)) {
            $result['type'] = strtolower(trim($matches[1]));
        }

        if (preg_match('/Content-Transfer-Encoding:\s*(\S+)/i', $headers, $matches)) {
            $result['encoding'] = strtolower(trim($matches[1]));
        }

        // Decode body based on encoding
        $result['content'] = $this->decodeByEncoding($body, $result['encoding']);

        return $result;
    }

    /**
     * Decode content by transfer encoding
     */
    protected function decodeByEncoding(string $content, string $encoding): string
    {
        $content = trim($content);

        switch (strtolower($encoding)) {
            case 'quoted-printable':
                return $this->decodeQuotedPrintable($content);
            
            case 'base64':
                return base64_decode($content);
            
            case '8bit':
            case '7bit':
            case 'binary':
            default:
                return $content;
        }
    }

    /**
     * Decode quoted-printable content
     */
    public function decodeQuotedPrintable(string $content): string
    {
        // First, use PHP's built-in decoder
        $decoded = quoted_printable_decode($content);
        
        // Additional cleanup for common artifacts
        // Remove soft line breaks (=\r\n or =\n)
        $decoded = preg_replace('/=\r?\n/', '', $decoded);
        
        // Clean up any remaining encoded sequences that might have been missed
        // Like =20 (space), =A0 (nbsp), etc
        $decoded = preg_replace_callback('/=([0-9A-F]{2})/i', function($matches) {
            return chr(hexdec($matches[1]));
        }, $decoded);

        return $decoded;
    }

    /**
     * Decode content (auto-detect encoding)
     */
    protected function decodeContent(string $content): string
    {
        $content = trim($content);

        // Check if it looks like quoted-printable
        if (preg_match('/=[0-9A-F]{2}/', $content)) {
            return $this->decodeQuotedPrintable($content);
        }

        // Check if it looks like base64 (long strings without spaces)
        if (preg_match('/^[A-Za-z0-9+\/=\r\n]{100,}$/', $content)) {
            $decoded = base64_decode($content, true);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        // Remove MIME headers if present
        $content = preg_replace('/^Content-Type:.*\r?\n/mi', '', $content);
        $content = preg_replace('/^Content-Transfer-Encoding:.*\r?\n/mi', '', $content);
        $content = preg_replace('/^Content-Disposition:.*\r?\n/mi', '', $content);
        $content = preg_replace('/^Mime-Version:.*\r?\n/mi', '', $content);
        
        return trim($content);
    }

    /**
     * Check if content looks like HTML
     */
    protected function looksLikeHtml(string $content): bool
    {
        return preg_match('/<html|<body|<div|<table|<p>/i', $content) > 0;
    }

    /**
     * Sanitize HTML for safe display
     */
    public function sanitizeHtml(string $html): string
    {
        // Remove potentially dangerous tags
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);
        $html = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', '', $html);
        $html = preg_replace('/<embed\b[^>]*>/is', '', $html);
        
        // Remove dangerous event handlers
        $html = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\bon\w+\s*=\s*\S+/i', '', $html);
        
        // Remove javascript: protocol
        $html = preg_replace('/javascript:/i', '', $html);
        
        // Clean up form actions
        $html = preg_replace('/<form\b[^>]*>/i', '<form action="#" method="get">', $html);

        // Force light mode by removing dark background styles
        $html = $this->convertToLightMode($html);

        return $html;
    }

    /**
     * Convert dark mode styles to light mode for better readability
     */
    protected function convertToLightMode(string $html): string
    {
        // Remove dark background colors
        $html = preg_replace('/background-color:\s*#(1c1c1e|000000|1c1c1c|2c2c2e|0d0d0d|111|222|333|eeeeee|eee)/i', 'background-color: #ffffff', $html);
        $html = preg_replace('/background:\s*#(1c1c1e|000000|1c1c1c|2c2c2e|0d0d0d|111|222|333|eeeeee|eee)/i', 'background: #ffffff', $html);
        
        // Convert dark rgba backgrounds
        $html = preg_replace('/background-color:\s*rgba?\(\s*28\s*,\s*28\s*,\s*30\s*[^)]*\)/i', 'background-color: #ffffff', $html);
        $html = preg_replace('/background:\s*rgba?\(\s*28\s*,\s*28\s*,\s*30\s*[^)]*\)/i', 'background: #ffffff', $html);
        
        // Convert light text colors to dark (for visibility on white background)
        $html = preg_replace('/color:\s*rgba?\(\s*235\s*,\s*235\s*,\s*245\s*[^)]*\)/i', 'color: #000000', $html);
        $html = preg_replace('/color:\s*#(ffffff|fff|f5f5f5|fafafa)/i', 'color: #000000', $html);
        
        // Normalize dark gray text colors (#1c1c1c, #747474) to pure black for consistency
        // This prevents CSS variable inheritance issues
        $html = preg_replace('/color:\s*#(1c1c1c|1c1c1e|2c2c2e|747474|666666|666|333333|333)/i', 'color: #000000', $html);
        $html = preg_replace('/color:\s*rgba?\(\s*28\s*,\s*28\s*,\s*28\s*[^)]*\)/i', 'color: #000000', $html);
        $html = preg_replace('/color:\s*rgba?\(\s*116\s*,\s*116\s*,\s*116\s*[^)]*\)/i', 'color: #666666', $html);
        
        // Keep link colors visible (blue shades)
        // But normalize them to consistent blue
        $html = preg_replace('/color:\s*#(2765cf|0066cc|0051d5|007aff)/i', 'color: #0066cc', $html);
        
        return $html;
    }

    /**
     * Extract preview text from HTML or plain text
     */
    public function extractPreview(string $content, int $length = 150): string
    {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Remove common email signatures
        $text = preg_split('/(-{2,}|Best regards|Regards|Terima kasih|Salam|Sent from)/i', $text, 2)[0];
        
        // Truncate
        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length) . '...';
        }
        
        return $text;
    }

    /**
     * Remove MIME boundaries from text
     */
    public function stripMimeBoundaries(string $content): string
    {
        // Remove MIME boundary lines
        $content = preg_replace('/^--[a-zA-Z0-9]+(--)?\r?\n/m', '', $content);
        
        // Remove Content-* headers
        $content = preg_replace('/^Content-[A-Za-z-]+:.*\r?\n/mi', '', $content);
        $content = preg_replace('/^Mime-Version:.*\r?\n/mi', '', $content);
        
        // Clean up extra newlines
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        return trim($content);
    }
}
