import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    return {
        server: process.env.NODE_ENV === 'development' ? {
            host: '0.0.0.0',
            hmr: {
                host: env.VITE_HOST || '0.0.0.0',
            },
        } : {},

        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
    };
});
