<?php

if (! function_exists('app_markdown')) {
    /**
     * Minimal, safe Markdown renderer for article bodies.
     * Supports: headings (###), bold (**), italic (*), blockquotes (>),
     * ordered (1.) and unordered (-) lists, and paragraph breaks.
     */
    function app_markdown(?string $text): string
    {
        if (blank($text)) {
            return '';
        }

        // Normalize line endings.
        $text = str_replace(["\r\n", "\r"], "\n", (string) $text);

        // Escape HTML first to prevent injection.
        $text = e($text);

        $lines = explode("\n", $text);
        $html = '';
        $inList = null; // 'ol' | 'ul' | null
        $inQuote = false;

        $closeList = function () use (&$inList, &$html) {
            if ($inList !== null) {
                $html .= "</{$inList}>";
                $inList = null;
            }
        };

        $closeQuote = function () use (&$inQuote, &$html) {
            if ($inQuote) {
                $html .= '</blockquote>';
                $inQuote = false;
            }
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Blank line -> close current blocks.
            if ($trimmed === '') {
                $closeList();
                $closeQuote();

                continue;
            }

            // Blockquote.
            if (str_starts_with($trimmed, '&gt; ') || str_starts_with($line, '> ')) {
                $content = str_starts_with($line, '> ')
                    ? substr($line, 2)
                    : substr($trimmed, 5); // "&gt; " is 5 chars after escaping
                $content = trim($content);

                $closeList();
                if (! $inQuote) {
                    $html .= '<blockquote>';
                    $inQuote = true;
                }
                $html .= '<p>'.self_markdown_inline($content).'</p>';

                continue;
            }

            // Ordered list item: "1." / "1. "
            if (preg_match('/^\d+\.\s+(.*)$/', $trimmed, $m)) {
                $closeQuote();
                if ($inList !== 'ol') {
                    $closeList();
                    $html .= '<ol>';
                    $inList = 'ol';
                }
                $html .= '<li>'.self_markdown_inline($m[1]).'</li>';

                continue;
            }

            // Unordered list item: "- " / "* "
            if (preg_match('/^[-*]\s+(.*)$/', $trimmed, $m)) {
                $closeQuote();
                if ($inList !== 'ul') {
                    $closeList();
                    $html .= '<ul>';
                    $inList = 'ul';
                }
                $html .= '<li>'.self_markdown_inline($m[1]).'</li>';

                continue;
            }

            // Heading: "### "
            if (preg_match('/^(#{1,3})\s+(.*)$/', $trimmed, $m)) {
                $level = strlen($m[1]) + 2; // ### -> h5 etc.
                $level = min(max($level, 3), 6);
                $closeList();
                $closeQuote();
                $html .= '<h'.$level.'>'.self_markdown_inline($m[2]).'</h'.$level.'>';

                continue;
            }

            // Regular paragraph.
            $closeList();
            $closeQuote();
            $html .= '<p>'.self_markdown_inline($trimmed).'</p>';
        }

        $closeList();
        $closeQuote();

        return $html;
    }
}

if (! function_exists('self_markdown_inline')) {
    /**
     * Inline formatting: bold (**), italic (*), and hard line breaks.
     * Escaping already done by caller.
     */
    function self_markdown_inline(string $text): string
    {
        // Bold first (longer markers).
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        // Italic (single *).
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        // Inline code.
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

        return $text;
    }
}
