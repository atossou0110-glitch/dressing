<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center border border-transparent bg-gradient-to-r from-[#d4aa55] to-[#b17f33] px-5 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 shadow-[0_16px_35px_rgba(212,170,85,0.32)] transition duration-150 ease-in-out hover:-translate-y-0.5 hover:shadow-[0_20px_40px_rgba(212,170,85,0.4)] focus:outline-none focus:ring-2 focus:ring-gold/60 focus:ring-offset-0']) }}>
    {{ $slot }}
</button>
