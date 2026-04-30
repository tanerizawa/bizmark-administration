import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/css/landing.css',
                'resources/css/inquiry-form.css',
                'resources/css/landing-theme.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
