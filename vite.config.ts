import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/ts/app.ts"],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/ts/src', import.meta.url)),
        },
    },
    base: '/',
    build: {
        chunkSizeWarningLimit: 3000,
    },
});
