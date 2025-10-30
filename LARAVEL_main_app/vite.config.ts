import vue from '@vitejs/plugin-vue';
// import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Allow connections from any device
        port: 5173, // You can change the port if needed
        strictPort: true, // Ensure that it only uses this port
        cors: true,
        hmr: {
            host: 'localhost', // Or your local IP
            // port: 5173, // Or your desired port
            // protocol: 'ws', // Use WebSocket protocol; default is HTTP
        }
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'resource': resolve(__dirname, './resources'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
            'volt-components': resolve(__dirname, './src/volt')
        },
    },
});
