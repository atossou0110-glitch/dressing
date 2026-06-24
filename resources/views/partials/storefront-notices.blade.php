@if (session('cart_success'))
    <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
        <div class="brand-notice brand-notice-success px-4 py-3 text-sm font-medium">
            {{ session('cart_success') }}
        </div>
    </div>
@endif
