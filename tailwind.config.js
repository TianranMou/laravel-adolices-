import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans], // Modern, friendly font
            },
            colors: {
                primary: '#3B82F6',   // Softer blue for a youthful vibe
                secondary: '#A855F7', // Lighter purple, still vibrant
                accent: '#FBBF24',    // Warm yellow for highlights
                neutral: '#F3F4F6',   // Light gray for backgrounds
            },
        },
    },

    plugins: [forms],
};