{{--
    Landing page — Home (V2 Redesign)
    Editorial Premium B2B — hybrid hub architecture
    Rendered via landing.id.index / landing.en.index
    Data passed by PublicArticleController@landing:
        $latestArticles, $services, $locale, $marketSegment

    Narrative arc (post-audit, May 2026):
      1. HERO          — value prop + free AI quick-check
      2. TRUST-STRIP   — establish credibility BEFORE problem framing
      3. PAIN-SOLUTION — problems we solve
      4. ECOSYSTEM-HUB — free tools (solution shown adjacent to problem)
      5. LIVE-PROGRESS — platform-in-action (real-time portal flow)
      6. SEGMENTATION  — "now pick your business type" (after solution shown)
      7. SERVICES      — managed offerings (anchor for SLA stats)
      8. PROCESS       — 4-step accountability + portal tracking
      9. CASE-STUDIES  — social proof
     10. FAQ           — objections (right after social proof, before content layer)
     11. ARTICLES      — thought leadership
     12. NEWSLETTER    — retention
     13. FINAL-CTA     — dual conversion (AI check / WhatsApp)
--}}
@php
    $locale = $locale ?? app()->getLocale();
@endphp

@include('landing.sections.v2.hero',          ['locale' => $locale])
@include('landing.sections.v2.trust-strip',   ['locale' => $locale])
@include('landing.sections.v2.pain-solution', ['locale' => $locale])
@include('landing.sections.v2.ecosystem-hub', ['locale' => $locale])
@include('landing.sections.v2.live-progress', ['locale' => $locale])
@include('landing.sections.v2.segmentation',  ['locale' => $locale])
@include('landing.sections.v2.services',      ['locale' => $locale])
@include('landing.sections.v2.process',       ['locale' => $locale])
@include('landing.sections.v2.case-studies',  ['locale' => $locale])
@include('landing.sections.v2.faq',           ['locale' => $locale])
@include('landing.sections.v2.articles',      ['locale' => $locale, 'latestArticles' => $latestArticles ?? collect()])
@include('landing.sections.v2.newsletter',    ['locale' => $locale])
@include('landing.sections.v2.final-cta',     ['locale' => $locale])
