import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite'

export default defineConfig({
    build: {
        sourcemap: false,
        rollupOptions: {
            cache: false,
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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

        /**
         * unplugin-vue-components plugin is responsible of autoloading components
         * documentation and md file are loaded for elements and components sections
         *
         * @see https://github.com/antfu/unplugin-vue-components
         */
        Components({
            // relative paths to the directory to search for components.
            dirs: ['resources/js/Components'],
            extensions: ['vue'],
            deep: true,
            dts: true,
            allowOverrides: false,
        })
    ],
});
