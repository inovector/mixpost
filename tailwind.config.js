const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    "50": "#EDECF8",
                    "100": "#DCDAF1",
                    "200": "#B8B4E4",
                    "300": "#958FD6",
                    "400": "#726AC8",
                    "500": "#4F46BB",
                    "600": "#3F3795",
                    "700": "#2F2970",
                    "800": "#1F1B4B",
                    "900": "#100E25"
                },
                stone: {
                    "50": "#FFFFFF",
                    "100": "#FFFFFF",
                    "200": "#FCFCFC",
                    "300": "#FCFCFC",
                    "400": "#FAFAFA",
                    "500": "#FAFAFA",
                    "600": "#C7C7C7",
                    "700": "#969696",
                    "800": "#636363",
                    "900": "#333333"
                },
                orange: {
                    "50": "#FFF5EB",
                    "100": "#FFEEDB",
                    "200": "#FFDEB8",
                    "300": "#FFCD94",
                    "400": "#FFBC70",
                    "500": "#FFAB4C",
                    "600": "#FF8D0A",
                    "700": "#C76A00",
                    "800": "#854700",
                    "900": "#422300"
                }
            },
            boxShadow: {
                'mix': '0 5px 10px rgb(55 55 89 / 8%)',
            }
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
