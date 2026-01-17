import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/nav.css',
                'resources/css/home.css',
                'resources/css/auth.css',
                'resources/css/product-details.css',
                'resources/css/cart.css',
                'resources/css/checkout.css',
                'resources/css/checkout-success.css',
                'resources/css/profile.css',
                'resources/css/admin-product.css',
                'resources/js/app.js',
                'resources/js/auth.js',
                'resources/js/product-details.js',
                'resources/js/cart.js',
                'resources/js/add-to-cart.js',
                'resources/js/filter.js',
                'resources/js/admin-product.js'
            ],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
});
