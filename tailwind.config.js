import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            gridTemplateColumns: {
                // Simple 16 column grid
                '16': 'repeat(16, minmax(0, 1fr))',
        
                // Complex site-specific column configuration
                'map': '24rem 1fr',
            },
            gridTemplateRows: {
                // Simple 16 column grid
                '16': 'repeat(16, minmax(0, 1fr))',

                // Complex site-specific column configuration
                'map': '1fr 8rem;',
            }
        },
    },

    plugins: [forms],
};
