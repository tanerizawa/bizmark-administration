@props([
    'data' => [],            // numeric array
    'width' => 80,
    'height' => 24,
    'color' => null,         // CSS var or hex; defaults to --client-primary
    'fill' => true,
    'strokeWidth' => 1.5,
    'ariaLabel' => null,
])

@php
    $values = array_values(array_map('floatval', $data));
    $count = count($values);
    if ($count < 2) {
        $values = [0, 0];
        $count = 2;
    }
    $min = min($values);
    $max = max($values);
    $range = ($max - $min) ?: 1;

    $points = [];
    foreach ($values as $i => $v) {
        $x = round(($i / ($count - 1)) * $width, 2);
        $y = round($height - (($v - $min) / $range) * ($height - $strokeWidth) - ($strokeWidth / 2), 2);
        $points[] = "{$x},{$y}";
    }
    $line = implode(' ', $points);
    $area = "0,{$height} " . $line . " {$width},{$height}";
    $stroke = $color ?? 'var(--client-primary)';
    $aria = $ariaLabel ?: "Tren {$count} titik";
@endphp

<svg {{ $attributes->merge(['class' => 'portal-spark']) }}
     viewBox="0 0 {{ $width }} {{ $height }}"
     width="{{ $width }}"
     height="{{ $height }}"
     preserveAspectRatio="none"
     role="img"
     aria-label="{{ $aria }}">
    @if($fill)
        <polygon points="{{ $area }}" fill="{{ $stroke }}" opacity="0.12"/>
    @endif
    <polyline points="{{ $line }}"
              fill="none"
              stroke="{{ $stroke }}"
              stroke-width="{{ $strokeWidth }}"
              stroke-linejoin="round"
              stroke-linecap="round"/>
</svg>
