@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'admin-input placeholder:text-[rgba(57,85,92,0.55)]']) }}>
