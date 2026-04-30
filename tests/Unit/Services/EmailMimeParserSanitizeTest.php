<?php

namespace Tests\Unit\Services;

use App\Services\EmailMimeParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailMimeParserSanitizeTest extends TestCase
{
    private EmailMimeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new EmailMimeParser;
    }

    #[Test]
    public function it_removes_script_tags(): void
    {
        $input = '<p>Hello</p><script>alert(1)</script><p>World</p>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('alert(1)', $clean);
        $this->assertStringContainsString('<p>Hello</p>', $clean);
        $this->assertStringContainsString('<p>World</p>', $clean);
    }

    #[Test]
    public function it_removes_iframe_tags(): void
    {
        $input = '<iframe src="https://evil.com"></iframe>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('<iframe', $clean);
    }

    #[Test]
    public function it_removes_object_and_embed_tags(): void
    {
        $input = '<object data="evil.swf"></object><embed src="evil.swf">';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('<object', $clean);
        $this->assertStringNotContainsString('<embed', $clean);
    }

    #[Test]
    public function it_removes_event_handlers(): void
    {
        $input = '<img src="x" onerror="alert(1)"><body onload="alert(1)">';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('onload', $clean);
    }

    #[Test]
    public function it_removes_javascript_protocol(): void
    {
        $input = '<a href="javascript:alert(1)">click</a>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('javascript:', $clean);
    }

    #[Test]
    public function it_removes_meta_refresh_redirects(): void
    {
        $input = '<meta http-equiv="refresh" content="0;url=https://evil.com">';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('meta', $clean);
    }

    #[Test]
    public function it_removes_base_tag(): void
    {
        $input = '<base href="https://evil.com">';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('<base', $clean);
    }

    #[Test]
    public function it_removes_link_stylesheet_injection(): void
    {
        $input = '<link rel="stylesheet" href="https://evil.com/malicious.css">';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('<link', $clean);
    }

    #[Test]
    public function it_neutralizes_form_actions(): void
    {
        $input = '<form action="https://evil.com/phish"><input type="text"></form>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringContainsString('<form action="#"', $clean);
        $this->assertStringNotContainsString('evil.com', $clean);
    }

    #[Test]
    public function it_blocks_encoded_javascript(): void
    {
        $input = '<a href="j&#97;vascript:alert(1)">click</a>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('javascript', $clean);
    }

    #[Test]
    public function it_removes_vbscript_protocol(): void
    {
        $input = '<a href="vbscript:msgbox(1)">click</a>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringNotContainsString('vbscript', $clean);
    }

    #[Test]
    public function it_blocks_data_uri_html_in_src(): void
    {
        $input = '<iframe src="data:text/html,<script>alert(1)</script>"></iframe>';
        $clean = $this->parser->sanitizeHtml($input);

        // iframe should be removed entirely
        $this->assertStringNotContainsString('<iframe', $clean);
    }

    #[Test]
    public function it_preserves_legitimate_email_html(): void
    {
        $input = '<div><p>Dear <b>Customer</b>,</p><p>Your permit <span style="color:blue;">NIB</span> is ready.</p><p>Regards,<br>BizMark Team</p></div>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringContainsString('Dear', $clean);
        $this->assertStringContainsString('<b>Customer</b>', $clean);
        $this->assertStringContainsString('BizMark Team', $clean);
        $this->assertStringContainsString('<br>', $clean);
    }

    #[Test]
    public function it_preserves_images_and_tables(): void
    {
        $input = '<table><tr><td><img src="https://bizmark.id/logo.png" alt="Logo"></td></tr></table>';
        $clean = $this->parser->sanitizeHtml($input);

        $this->assertStringContainsString('<table>', $clean);
        $this->assertStringContainsString('<img', $clean);
        $this->assertStringContainsString('bizmark.id', $clean);
    }
}
