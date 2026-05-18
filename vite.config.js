import fs from "fs";
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
    plugins: glob.sync(`${ASSET_ROOT}/plugins/*/*.{css,js}`),
};

// Helpers: Function to generate entry points
function entries() {
    return [...PATHS.core, ...PATHS.pages, ...PATHS.plugins];
}

// Helpers: Recursively copy a directory
function copyDir(src, dest) {
    if (!fs.existsSync(src)) return;
    fs.mkdirSync(dest, { recursive: true });
    for (const entry of fs.readdirSync(src, { withFileTypes: true })) {
        if (entry.name === ".DS_Store") continue;
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);
        if (entry.isDirectory()) {
            copyDir(srcPath, destPath);
        } else {
            fs.copyFileSync(srcPath, destPath);
        }
    }
}

// Plugin: Copy static medias to the build output directory
function copyStaticMedias() {
    const src = path.resolve(ASSET_ROOT, "medias");
    const dest = path.resolve("public/build/medias");
    return {
        name: "copy-static-medias",
        apply: "build",
        closeBundle() {
            copyDir(src, dest);
        },
    };
}

// Helpers: Resolve output sub-path for plugin entries/assets
function pluginOutputPath(sourcePath, ext) {
    const rel = sourcePath.split(`${ASSET_ROOT}/plugins/`)[1];
    const dir = path.dirname(rel);
    const name = path.basename(rel, path.extname(rel));
    return `plugins/${dir}/${name}-[hash]${ext}`;
}

export default defineConfig({
    plugins: [
        laravel({
            input: entries(),
            refresh: true,
        }),
        tailwindcss(),
        copyStaticMedias(),
    ],
    build: {
        rollupOptions: {
            output: {
                entryFileNames: (chunk) => {
                    const id = chunk.facadeModuleId || "";
                    if (id.includes(`/${ASSET_ROOT}/plugins/`)) {
                        return pluginOutputPath(id, ".js");
                    }
                    if (id.includes(`/${ASSET_ROOT}/js/pages/`)) {
                        return "js/pages/[name]-[hash].js";
                    }
                    return "js/[name]-[hash].js";
                },
                chunkFileNames: "js/chunks/[name]-[hash].js",
                assetFileNames: (asset) => {
                    const original =
                        (asset.originalFileNames &&
                            asset.originalFileNames[0]) ||
                        asset.originalFileName ||
                        "";
                    const ext = path.extname(asset.name || "");
                    if (original.includes(`${ASSET_ROOT}/plugins/`)) {
                        return pluginOutputPath(original, ext);
                    }
                    const extName = ext.slice(1).toLowerCase();
                    if (extName === "css") {
                        return "css/[name]-[hash][extname]";
                    }
                    if (
                        ["woff", "woff2", "ttf", "eot", "otf"].includes(extName)
                    ) {
                        return "fonts/[name]-[hash][extname]";
                    }
                    if (
                        [
                            "png",
                            "jpg",
                            "jpeg",
                            "gif",
                            "svg",
                            "webp",
                            "avif",
                            "ico",
                        ].includes(extName)
                    ) {
                        return "images/[name]-[hash][extname]";
                    }
                    return "assets/[name]-[hash][extname]";
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
