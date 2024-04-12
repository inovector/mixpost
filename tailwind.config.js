const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
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
                red: {
                    "50": "#FDEDED",
                    "100": "#FBDADC",
                    "200": "#F7B6B9",
                    "300": "#F3969A",
                    "400": "#EF7177",
                    "500": "#EB4D55",
                    "600": "#E11923",
                    "700": "#AA131B",
                    "800": "#6E0C11",
                    "900": "#370609"
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
                },
                cyan: {
                    "50": "#F1FDFE",
                    "100": "#E7FBFD",
                    "200": "#D0F7FB",
                    "300": "#B4F2F9",
                    "400": "#9CEEF7",
                    "500": "#84E9F5",
                    "600": "#3EDEEF",
                    "700": "#11BCD0",
                    "800": "#0B818E",
                    "900": "#064047"
                },
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
                facebook: '#1877f2',
                twitter: '#000000',
                mastodon: '#6364FF'
            },
            boxShadow: {
                'mix': '0 5px 10px rgb(55 55 89 / 8%)',
            },
            spacing: {
                'xs': '0.5rem', // 2
                'sm': '0.75rem', // 3
                'md': '1rem', // 4
                'lg': '1.5rem', // 6
                'xl': '2rem', // 8
                '2xl': '2.5rem', // 10
            },
            gridTemplateColumns: {
                'week-time-sm': '48px repeat(7, 1fr)',
                'week-time': '70px repeat(7, 1fr)'
            }
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
