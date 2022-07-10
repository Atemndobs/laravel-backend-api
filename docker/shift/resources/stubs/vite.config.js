import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
{{ imports }}

export default defineConfig({
    plugins: [
        laravel([
{{ entrypoints }}
        ]),
{{ plugins }}
    ],
});
