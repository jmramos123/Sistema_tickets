import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: '0.0.0.0',          // listen on all interfaces
    port: 5173,
    strictPort: true,         // fail if 5173 is in use
    hmr: {
      host: '192.168.3.11',   // your dev machine’s LAN IP
      //host: '192.168.26.3',   // your dev machine’s LAN IP
      protocol: 'ws',         // or 'wss' if you’ve got TLS
      port: 5173,
    },
    cors: true,               // enable CORS headers
  },
});
