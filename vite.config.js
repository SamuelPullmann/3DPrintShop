import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/home.css',
                'resources/css/auth.css',
                'resources/css/nav.css',
                'resources/css/product-details.css',
                'resources/js/app.js',
                'resources/js/product-details.js'
            ],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
});
