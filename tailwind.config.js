import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './node_modules/flowbite/**/*.js',
    ],

    safelist: [
        'bg-gray-300',
        'bg-red-500',
        'bg-orange-500',
        'bg-brand-500',
        'bg-forest-600',
        'bg-brand-100',
        'text-gray-900',
        'text-red-600',
        'text-orange-600',
        'text-brand-600',
        'text-forest-600',
        'font-medium',
        'text-gray-500',
        // e-Wallet provider cards (also inlined in Blade; safelist keeps builds stable)
        'border-blue-100', 'border-blue-500', 'bg-blue-50', 'bg-blue-50/40', 'ring-blue-200', 'text-blue-600',
        'border-green-100', 'border-green-500', 'bg-green-50', 'bg-green-50/40', 'ring-green-200', 'text-green-600',
        'border-emerald-100', 'border-emerald-500', 'bg-emerald-50', 'bg-emerald-50/40', 'ring-emerald-200', 'text-emerald-600',
        'border-orange-100', 'border-orange-500', 'bg-orange-50', 'bg-orange-50/40', 'ring-orange-200',
        'border-yellow-100', 'border-yellow-500', 'bg-yellow-50', 'bg-yellow-50/40', 'ring-yellow-200', 'text-yellow-700',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                logo: ['"Suez One"', 'serif'],
            },
            colors: {
                brand: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                },
                accent: {
                    DEFAULT: '#FFC107',
                    400: '#FFD54F',
                    500: '#FFC107',
                    600: '#FFB300',
                },
                forest: {
                    DEFAULT: '#003D29',
                    600: '#003D29',
                    700: '#002818',
                    800: '#001f13',
                },
                sage: {
                    200: '#d8e8dc',
                    300: '#b8d4be',
                    400: '#95B49F',
                },
                cream: {
                    DEFAULT: '#FFF1E6',
                    50: '#FFFAF5',
                    100: '#FFF1E6',
                    200: '#F5E6D8',
                },
                avocado: {
                    50: '#f4f7ef',
                    100: '#e8f0dc',
                    200: '#d8e8c8',
                    300: '#c5d9b0',
                    400: '#a8c686',
                },
            },
        },
    },

    plugins: [forms, typography, flowbite],
};
