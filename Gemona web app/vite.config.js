import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json']
    },
    optimizeDeps: {
        include: ["quill"],
        exclude: ["swiper/vue", "swiper/types"]
    },
    // server: {
    //     host: '0.0.0.0',
    //     hmr: {
    //         host: 'localhost'
    //     }
    // }
});