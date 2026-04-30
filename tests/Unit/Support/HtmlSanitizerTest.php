<?php

namespace Tests\Unit\Support;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    #[Test]
    public function it_removes_scripts_and_event_handlers(): void
    {
        $input = '<p>Hello</p><img src=x onerror="alert(1)"><script>alert(1)</script>';
        $clean = HtmlSanitizer::clean($input);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringContainsString('<p>Hello</p>', $clean);
    }

    #[Test]
    public function it_blocks_javascript_urls_and_sets_safe_rel(): void
    {
        $input = '<a href="javascript:alert(1)" target="_blank">click</a>';
        $clean = HtmlSanitizer::clean($input);

        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('rel="nofollow noopener noreferrer"', $clean);
    }
}
