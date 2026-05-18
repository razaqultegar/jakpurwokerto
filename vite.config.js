import * as glob from "glob";
import path from "path";
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

// Helpers: Define asset paths
const ASSET_ROOT = "resources/assets";
const PATHS = {
    core: [`${ASSET_ROOT}/css/app.css`, `${ASSET_ROOT}/js/app.js`],
    pages: glob.sync(`${ASSET_ROOT}/js/pages/*.js`),
};

// Helpers: Function to generate entry points
function entries() {
    return [...PATHS.core, ...PATHS.pages];
}

export default defineConfig({
    plugins: [
        laravel({
            input: entries(),
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
