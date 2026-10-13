import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/main.js', 'resources/css/main.css', 'resources/js/toast.js', 'resources/css/toast.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            hmr: {
                host: '192.168.1.44',
            },
            //TODO: TCP
        }, //TCP port 8000 PHP 
        // vite also do it
});
