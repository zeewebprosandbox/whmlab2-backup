import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css'],
            refresh: [
                'resources/views/**/*.blade.php',
                'app/Http/Controllers/**/*.php',
                'app/View/Components/**/*.php',
            ],
        }),
    ],
});
