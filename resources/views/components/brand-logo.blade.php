@props([
    'compact' => false,
])

<span {{ $attributes->merge(['class' => 'king-brand-lockup']) }}>
    <span class="king-brand-word">king</span>
    <span class="king-brand-line">
        <span class="king-brand-sub">Rangement</span>
        @unless ($compact)
            <span class="king-brand-tagline">Chaque chose a sa place</span>
        @endunless
    </span>
</span>
