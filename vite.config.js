import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import os from 'os';

const getLocalIP = () => {
  const interfaces = os.networkInterfaces();
  for (const name of Object.keys(interfaces)) {
    for (const iface of interfaces[name]) {
      if (iface.family === 'IPv4' && !iface.internal) {
        return iface.address;
      }
    }
  }
  return 'localhost';
};

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
      host: getLocalIP(),     // automatically detect LAN IP
      protocol: 'ws',         // or 'wss' if you’ve got TLS
      port: 5173,
    },
    cors: true,               // enable CORS headers
  },
});
