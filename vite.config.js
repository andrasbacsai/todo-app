import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: process.env.NODE_ENV === 'development' ? {
        host: '0.0.0.0',
        hmr: {
            host: '0.0.0.0',
        },
    } : {},

    plugins: [
        laravel({
            input: ["resources/css/luvi-ui.css", 'resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
