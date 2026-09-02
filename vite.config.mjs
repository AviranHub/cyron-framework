import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

// تعریف دستی __filename و __dirname برای محیط ESM (ماژول‌های جدید)
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
    root: __dirname,
    
    server: {
        port: 5173,
        hmr: true,
        proxy: {
            '/(?!(build|assets|@vite|node_modules)).*': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
        },
        watch: {
            usePolling: true,
            interval: 100,
        },
    },

    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                // حالا path.resolve به درستی کار می‌کند چون __dirname را دستی تعریف کردیم
                script: path.resolve(__dirname, 'resources/Scripts/script.js'),
                style: path.resolve(__dirname, 'resources/Styles/style.css'),
            },
        },
    },

    plugins: [
        {
            name: 'full-reload',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.php') || file.endsWith('.lady.php')) {
                    server.ws.send({ type: 'full-reload' });
                }
            },
        },
    ],
});