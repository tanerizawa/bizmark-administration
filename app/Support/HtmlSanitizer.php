<?php

namespace App\Support;

final class HtmlSanitizer
{
    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return self::sanitizeDom($html);
    }

    private static function sanitizeDom(string $html): string
    {
        $allowedTags = [
            'a', 'b', 'blockquote', 'br', 'code', 'div', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr',
            'i', 'li', 'ol', 'p', 'pre', 'span', 'strong', 'u', 'ul',
        ];

        $allowedAttributes = [
            'a' => ['href', 'title', 'target', 'rel'],
            'div' => [],
            'span' => [],
        ];

        $previous = libxml_use_internal_errors(true);
        $doc = new \DOMDocument;

        $wrapped = '<?xml encoding="UTF-8">'.$html;
        $loaded = $doc->loadHTML($wrapped, \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return e(strip_tags($html));
        }

        $xpath = new \DOMXPath($doc);

        foreach ($xpath->query('//*') as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (! in_array($tag, $allowedTags, true)) {
                $textNode = $doc->createTextNode($node->textContent);
                $node->parentNode?->replaceChild($textNode, $node);

                continue;
            }

            $allowedForTag = $allowedAttributes[$tag] ?? [];
            $attributes = [];
            foreach (iterator_to_array($node->attributes ?? []) as $attr) {
                if ($attr instanceof \DOMAttr) {
                    $attributes[] = $attr->name;
                }
            }

            foreach ($attributes as $name) {
                $lower = strtolower($name);
                if (str_starts_with($lower, 'on')) {
                    $node->removeAttribute($name);

                    continue;
                }

                if ($lower === 'style') {
                    $node->removeAttribute($name);

                    continue;
                }

                if (! in_array($lower, $allowedForTag, true)) {
                    $node->removeAttribute($name);
                }
            }

            if ($tag === 'a') {
                $href = trim((string) $node->getAttribute('href'));
                if ($href !== '') {
                    $scheme = strtolower((string) parse_url($href, \PHP_URL_SCHEME));
                    if ($scheme !== '' && ! in_array($scheme, ['http', 'https', 'mailto', 'tel'], true)) {
                        $node->removeAttribute('href');
                    }
                }

                $target = strtolower((string) $node->getAttribute('target'));
                if ($target !== '' && $target !== '_blank') {
                    $node->removeAttribute('target');
                }

                $node->setAttribute('rel', 'nofollow noopener noreferrer');
            }
        }

        $result = $doc->saveHTML();
        if ($result === false) {
            return e(strip_tags($html));
        }

        return $result;
    }
}
