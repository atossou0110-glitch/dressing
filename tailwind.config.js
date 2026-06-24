import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    safelist: [
        'animate-glass-slide-in',
        'animate-stagger-in',
        'animate-text-reveal',
        'animate-parallax-float',
        'animate-button-pulse',
        'animate-drawer-slide',
        'animate-smooth-drawer',
        'animate-skeleton-loading',
        'animate-progress-fill',
        'animate-char-reveal',
        'hover-image-zoom',
        'hover-card-lift',
        'link-underline',
        'smooth-transition',
        'scroll-animation',
        'backdrop-blur-heavy',
    ],

    theme: {
        extend: {
            colors: {
                // Elegant palette
                'cream': '#f5f3f0',
                'beige': '#e8dcc4',
                'tan': '#c9b89a',
                'bronze': '#8b7355',
                'dark-brown': '#3a3a3a',
                'off-white': '#faf8f6',
                'charcoal': '#2a2a2a',
                'gold': '#d0a44f',
                'panel': '#14100c',
                'muted': '#b8a686',
                'accent': '#a67c3c',
            },
            fontFamily: {
                sans: ['Sora', ...defaultTheme.fontFamily.sans],
                serif: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif],
            },
            fontSize: {
                'xs': '0.7rem',
                'sm': '0.82rem',
                'base': '0.94rem',
                'lg': '1.02rem',
                'xl': '1.15rem',
                '2xl': '1.35rem',
                '3xl': '1.65rem',
                '4xl': '2rem',
                '5xl': '2.65rem',
                '6xl': '3.2rem',
            },
        },
    },

    plugins: [forms],
};
