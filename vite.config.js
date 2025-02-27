// @ts-check

import fs from 'node:fs';
import path from 'node:path';

import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';
import coffee from '@bkuri/rollup-plugin-coffeescript';

/**
 * Resolve function for paths
 *
 * @param {string[]} segments
 */
function resolvePath(...segments) {
  return path.resolve(import.meta.dirname, ...segments);
}

// Entry points
const entrypointDirs = ['resources/css/entrypoints', 'resources/js/entrypoints'];
const supportedExts = new Set(['.coffee', '.less', '.ts', '.tsx']);

/** @type {string[]} */
const entry = [];

// Dynamically populate entry points
for (const entrypoint of entrypointDirs) {
  const items = fs.readdirSync(resolvePath(entrypoint), { withFileTypes: true });

  for (const item of items) {
    if (!item.isFile()) continue;

    const filename = item.name;
    const ext = path.extname(filename);

    if (!supportedExts.has(ext)) continue;

    entry.push(resolvePath(entrypoint, filename));
  }
}

export default defineConfig({
  build: {
    manifest: true,
  },
  plugins: [
    laravel({
      input: entry,
    }),
    tsconfigPaths(),
    react(),
    coffee(),
  ],
  resolve: {
    alias: {
      '@fonts': resolvePath('resources/fonts'),
      '@images': resolvePath('public/images'),
       "*": ["js/*", "builds/*"]
    },
  },
});
