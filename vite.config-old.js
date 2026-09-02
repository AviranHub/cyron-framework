import { defineConfig } from 'vite';
import path from 'path';
import { Script } from 'vm';

export default defineConfig({
    // مسیر ریشه پروژه (جایی که فایل vite.config.js قرار دارد)
    root: path.resolve(__dirname),
    
    // تنظیمات سرور توسعه (اختیاری)
    server: {
        // اگر می‌خواهید در حین توسعه، درخواست‌های API به سرور PHP هدایت شود
        proxy: {
            '/(?!(build|assets)).*': {
                target: 'http://localhost:8000', // آدرس سرور PHP (مثلاً php artisan serve یا Apache)
                changeOrigin: true,
            },
        },
    },

    // فایل‌های ورودی
    build: {
        outDir: 'public/build',        // خروجی در public/build
        emptyOutDir: true,
        manifest: true,                // فایل manifest.json تولید می‌شود (مفید برای لود خودکار)
        rollupOptions: {
            input: {
                script: path.resolve(__dirname, 'resources/Scripts/script.js'),
                style: path.resolve(__dirname, 'resources/Styles/style.css'),
            },
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name].[ext]',
            },
        },
    },

    // پلاگین‌ها (مثل Tailwind CSS)
    plugins: [
        // اگر نیاز به پردازش CSS خاصی دارید
    ],
});