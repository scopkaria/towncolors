import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    primary: 'var(--primary-color)',
                    hover: 'var(--secondary-color)',
                    surface: 'var(--background-color)',
                    ink: 'var(--color-ink)',
                    muted: 'var(--color-muted)',
                },
            },
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Sora', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                panel: '0 24px 60px -30px rgba(15, 23, 42, 0.26)',
                card: '0 18px 40px -28px rgba(15, 23, 42, 0.24)',
            },
            keyframes: {
                'page-fade': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'page-fade': 'page-fade 0.55s ease-out both',
            },
        },
    },

    plugins: [forms],
};
