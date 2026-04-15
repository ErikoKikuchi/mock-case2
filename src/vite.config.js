import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js',
                'resources/js/admin/attendance-detail.js',
                'resources/js/admin/attendance-index.js',
                'resources/js/admin/login.js',
                'resources/js/admin/request-approve.js',
                'resources/js/admin/request-index.js',
                'resources/js/admin/user-attendance.js',
                'resources/js/admin/user-index.js',
                'resources/js/users/attendance-detail.js',
                'resources/js/users/attendance-index.js',
                'resources/js/users/login.js',
                'resources/js/users/register.js',
                'resources/js/users/request-index.js',
                'resources/js/users/verify-email.js',
                'resources/js/common.js',
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
    build:{
        outDir: 'public/build',
        emptyOutDir:true,
    },
});
