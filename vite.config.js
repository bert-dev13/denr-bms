import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/Login.css', 
                'resources/css/sidebar.css', 
                'resources/css/species-observation-modal.css',
                'resources/css/species-observations.css',
                'resources/css/protected-areas.css',
                'resources/js/bootstrap.js', 
                'resources/js/Login.js', 
                'resources/js/sidebar.js', 
                'resources/js/species-observation-modal.js',
                'resources/js/species-observations.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
