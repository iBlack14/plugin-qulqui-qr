import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    lib: {
      entry: resolve(__dirname, 'src/index.ts'),
      name: 'CulqiQR',
      formats: ['es', 'cjs'],
      fileName: (format) => `index.${format === 'es' ? 'mjs' : 'js'}`,
    },
    rollupOptions: {
      external: ['axios', 'eventemitter3'],
      output: {
        globals: {
          axios: 'axios',
          eventemitter3: 'EventEmitter',
        },
      },
    },
    sourcemap: true,
    minify: 'terser',
  },
});
