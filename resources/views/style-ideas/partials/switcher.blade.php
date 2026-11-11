{{-- The plain strip across the top of every mock-up. Deliberately unstyled so it is not mistaken for part of the design. --}}
<div style="background:#000;color:#fff;font:14px/1.4 system-ui,sans-serif;padding:8px 16px;display:flex;flex-wrap:wrap;gap:6px 20px;align-items:center;">
    <span>
        <strong>Style idea: {{ $styles[$currentStyleSlug]['name'] }}.</strong>
        Example data. Nothing here is real and the buttons do nothing.
    </span>
    <span style="margin-left:auto;display:flex;flex-wrap:wrap;gap:6px 16px;">
        @foreach ($styles as $styleSlug => $style)
            @if ($styleSlug !== $currentStyleSlug)
                <a href="{{ route('style-ideas.show', $styleSlug) }}" style="color:#fff;text-decoration:underline;">{{ $style['name'] }}</a>
            @endif
        @endforeach
        <a href="{{ route('style-ideas.index') }}" style="color:#fff;text-decoration:underline;">All style ideas</a>
    </span>
</div>
