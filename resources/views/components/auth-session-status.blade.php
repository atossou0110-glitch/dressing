@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'admin-status admin-status-success']) }}>
        {{ $status }}
    </div>
@endif
