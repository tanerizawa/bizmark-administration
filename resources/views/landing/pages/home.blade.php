{{--
    Landing page — Home (V2 Redesign)
    Editorial Premium B2B — hybrid hub architecture
    Rendered via landing.id.index / landing.en.index
    Data passed by PublicArticleController@landing:
        $latestArticles, $services, $locale, $marketSegment
--}}
@php
    $locale = $locale ?? app()->getLocale();
@endphp

@include('landing.sections.v2.hero',          ['locale' => $locale])
@include('landing.sections.v2.trust-strip',   ['locale' => $locale])
@include('landing.sections.v2.segmentation',  ['locale' => $locale])
@include('landing.sections.v2.ecosystem-hub', ['locale' => $locale])
@include('landing.sections.v2.services',      ['locale' => $locale])
@include('landing.sections.v2.pain-solution', ['locale' => $locale])
@include('landing.sections.v2.process',       ['locale' => $locale])
@include('landing.sections.v2.case-studies',  ['locale' => $locale])
@include('landing.sections.v2.articles',      ['locale' => $locale, 'latestArticles' => $latestArticles ?? collect()])
@include('landing.sections.v2.faq',           ['locale' => $locale])
@include('landing.sections.v2.newsletter',    ['locale' => $locale])
@include('landing.sections.v2.final-cta',     ['locale' => $locale])
