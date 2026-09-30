import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { google, local } from 'laravel-vite-plugin/fonts';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                // Local variable files: the google() provider only requests the ital/wght
                // axes, which would drop Fraunces' SOFT, WONK and opsz axes.
                local('Fraunces', {
                    alias: 'display',
                    variants: [
                        { src: 'resources/fonts/fraunces/fraunces-normal.woff2', weight: '300 700', style: 'normal' },
                        { src: 'resources/fonts/fraunces/fraunces-italic.woff2', weight: '300 700', style: 'italic' },
                    ],
                    fallbacks: ['Iowan Old Style', 'Palatino Linotype', 'Georgia', 'serif'],
                    preload: [{ weight: '300 700', style: 'normal' }],
                }),
                local('Literata', {
                    alias: 'body',
                    variants: [
                        { src: 'resources/fonts/literata/literata-normal.woff2', weight: '300 600', style: 'normal' },
                        { src: 'resources/fonts/literata/literata-italic.woff2', weight: '300 600', style: 'italic' },
                    ],
                    fallbacks: ['Iowan Old Style', 'Georgia', 'serif'],
                    preload: [{ weight: '300 600', style: 'normal' }],
                }),
                google('IBM Plex Mono', {
                    alias: 'mono',
                    weights: [400, 500],
                    fallbacks: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
                    preload: false,
                }),
            ],
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
