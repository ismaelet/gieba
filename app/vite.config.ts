import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/css/filament/admin/theme.css",
        "resources/js/app.ts",
        "resources/js/filament/admin.js",
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
  server: {
    hmr: {
      host: "localhost",
    },
  },
});
