import react from '@vitejs/plugin-react';
import {defineConfig} from 'vite';
import tailwindcss from '@tailwindcss/vite';
import {fileURLToPath, URL} from 'node:url';

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
            '@components': fileURLToPath(new URL('./src/components', import.meta.url)),
            '@layout': fileURLToPath(new URL('./src/components/layout', import.meta.url)),
            '@ui': fileURLToPath(new URL('./src/components/ui', import.meta.url)),
            '@data': fileURLToPath(new URL('./src/data', import.meta.url)),
            '@models': fileURLToPath(new URL('./src/types', import.meta.url)),
        },
    },
});