import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    publicDir: 'vendor/mixpost',
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            publicDirectory: 'resources/dist',
            buildDirectory: 'vendor/mixpost',
            refresh: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@css': '/resources/css',
            '@img': 'resources/img'
        },
    }
});
