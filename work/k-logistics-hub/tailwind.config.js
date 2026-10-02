/*
 * Tailwind settings for the K-logistics Hub case study page only.
 * This page uses a pre-built stylesheet (page.css) instead of the
 * Tailwind CDN script, so it loads much faster.
 *
 * After changing any classes in index.php, rebuild page.css from this
 * folder with:
 *   npx tailwindcss@3 -c tailwind.config.js -i page.src.css -o page.css --minify
 */
module.exports = {
    content: [
        './index.php',
        '../../partials/header.php',
        '../../partials/footer.php',
        '../../partials/project-nav.php',
    ],
    // Classes the shared partials build from $go_accent_class.
    safelist: ['text-kl-orange', 'hover:text-kl-orange'],
    theme: {
        extend: {
            colors: {
                'matte-black': '#101010',
                'card-dark': '#0a0a0a',
                'lavender': '#e9d5ff',
                'sharp-purple': '#7e22ce',
                'off-white': '#f5f5f5',
                'kl-navy': '#13203d',
                'kl-deep': '#0b1428',
                'kl-orange': '#ea7c0a',
            },
            fontFamily: {
                'syne': ['Syne', 'sans-serif'],
                'manrope': ['Manrope', 'sans-serif'],
            },
            backgroundImage: {
                'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
            },
        },
    },
};
