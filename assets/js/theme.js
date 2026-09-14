/**
 * GetOnline Studio — shared brand tokens.
 * Loaded before each page's `tailwind.config` so every page pulls the
 * core palette, fonts and noise texture from one place instead of
 * retyping hex codes per page. Pages still extend this with their own
 * one-off animations/keyframes.
 */
window.GO_COLORS = {
    'matte-black': '#101010',
    'card-dark': '#0a0a0a',
    'lavender': '#e9d5ff',
    'sharp-purple': '#7e22ce',
    'off-white': '#f5f5f5',
};

window.GO_FONTS = {
    'syne': ['Syne', 'sans-serif'],
    'manrope': ['Manrope', 'sans-serif'],
};

window.GO_NOISE_BG = "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')";
