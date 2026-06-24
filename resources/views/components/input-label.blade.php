@props(['value'])

<label {{ $attributes->merge(['class' => 'admin-field-label']) }}>
    {{ $value ?? $slot }}
</label>
