import * as esbuild from 'esbuild';

await esbuild.build({
  entryPoints: ['resources/js/firebase-auth.js'],
  bundle: true,
  format: 'iife',
  globalName: 'TaxpiyaFirebase',
  outfile: 'public/js/firebase-auth.bundle.js',
  platform: 'browser',
  target: ['es2020'],
  sourcemap: true,
});

console.log('Built public/js/firebase-auth.bundle.js');
