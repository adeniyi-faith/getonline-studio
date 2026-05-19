<?php
/**
 * Studio pSEO — PWA Install Page
 * Place this file in the same folder as studio-admin.php (the /wp/ folder)
 */
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect('https://getonlinestudio.com/wp/u-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Install Studio pSEO App</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Studio pSEO">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Studio pSEO">
    <meta name="theme-color" content="#7e22ce">
    <meta name="msapplication-TileColor" content="#7e22ce">
    <meta name="msapplication-TileImage" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- Icons -->
    <link rel="icon" type="image/jpeg" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- Manifest -->
    <link rel="manifest" href="/wp/manifest.json">

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#0a0a0a', 'panel-dark': '#121212', 'card-dark': '#1a1a1a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce', 'success-green': '#4ade80',
                    },
                    fontFamily: { 'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0a; color: #e9d5ff; font-family: 'Manrope', sans-serif; }
        @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 20px rgba(126,34,206,0.3); } 50% { box-shadow: 0 0 40px rgba(126,34,206,0.6); } }
        .glow { animation: pulse-glow 2s ease-in-out infinite; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.5s ease forwards; }
        .step-card { transition: all 0.2s ease; }
        .step-card:hover { border-color: rgba(126,34,206,0.4); }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 relative overflow-hidden">

    <!-- Background glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-sharp-purple/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[200px] bg-blue-500/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-lg fade-in">

        <!-- Header -->
        <div class="text-center mb-10">
            <div class="w-24 h-24 rounded-2xl mx-auto mb-6 overflow-hidden border-2 border-sharp-purple/30 glow">
                <img src="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg"
                     alt="Studio pSEO" class="w-full h-full object-cover">
            </div>
            <h1 class="font-syne text-3xl font-bold text-white mb-2">Studio <span class="text-sharp-purple">pSEO</span></h1>
            <p class="text-lavender/60 text-sm">Install the app for the best experience — works offline, launches instantly.</p>
        </div>

        <!-- Install Button (shown when prompt is available) -->
        <div id="install-section" class="hidden mb-6">
            <button id="install-btn"
                class="w-full py-4 bg-sharp-purple text-white font-syne font-bold text-base rounded-xl
                       shadow-[0_0_30px_rgba(126,34,206,0.4)] hover:bg-white hover:text-matte-black
                       transition-all flex items-center justify-center gap-3">
                <i data-lucide="download" class="w-5 h-5"></i>
                Install App
            </button>
            <p class="text-center text-xs text-lavender/40 mt-3">Tap the button above to add Studio pSEO to your home screen.</p>
        </div>

        <!-- Already installed / no prompt -->
        <div id="open-section" class="mb-6">
            <a href="/wp/studio-admin.php"
               class="w-full py-4 bg-success-green/10 border border-success-green/30 text-success-green font-syne font-bold text-base rounded-xl
                      hover:bg-success-green hover:text-black transition-all flex items-center justify-center gap-3">
                <i data-lucide="external-link" class="w-5 h-5"></i>
                Open Command Center
            </a>
        </div>

        <!-- Step-by-step instructions -->
        <div class="bg-panel-dark border border-white/5 rounded-2xl overflow-hidden mb-6">
            <div class="p-5 border-b border-white/5">
                <h2 class="font-syne font-bold text-white text-sm">How to Install</h2>
                <p class="text-lavender/50 text-xs mt-1">Follow the steps for your device below.</p>
            </div>

            <!-- Android / Chrome -->
            <div id="instructions-android" class="p-5 space-y-4">
                <h3 class="font-bold text-xs uppercase tracking-widest text-sharp-purple flex items-center gap-2">
                    <i data-lucide="smartphone" class="w-3.5 h-3.5"></i> Android / Chrome
                </h3>
                <div class="space-y-3">
                    <?php
                    $steps_android = [
                        ['download', 'Tap the Install App button above, or tap the ⋮ menu in Chrome.'],
                        ['plus-circle', 'Select "Add to Home Screen" or "Install App".'],
                        ['check-circle', 'Tap "Add" to confirm. The app will appear on your home screen.'],
                    ];
                    foreach ($steps_android as $i => [$icon, $text]):
                    ?>
                    <div class="step-card flex items-start gap-4 p-3 rounded-xl border border-white/5 bg-card-dark">
                        <div class="w-8 h-8 rounded-lg bg-sharp-purple/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-sharp-purple font-syne font-bold text-sm"><?= $i + 1 ?></span>
                        </div>
                        <p class="text-sm text-lavender/70 leading-relaxed pt-1"><?= $text ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="border-t border-white/5"></div>

            <!-- iOS / Safari -->
            <div id="instructions-ios" class="p-5 space-y-4">
                <h3 class="font-bold text-xs uppercase tracking-widest text-blue-400 flex items-center gap-2">
                    <i data-lucide="tablet-smartphone" class="w-3.5 h-3.5"></i> iPhone / iPad (Safari)
                </h3>
                <div class="space-y-3">
                    <?php
                    $steps_ios = [
                        'Open this page in <strong class="text-white">Safari</strong> (not Chrome or Firefox).',
                        'Tap the <strong class="text-white">Share</strong> icon (the box with an arrow pointing up) at the bottom of the screen.',
                        'Scroll down and tap <strong class="text-white">"Add to Home Screen"</strong>.',
                        'Tap <strong class="text-white">"Add"</strong> in the top right. Done!',
                    ];
                    foreach ($steps_ios as $i => $text):
                    ?>
                    <div class="step-card flex items-start gap-4 p-3 rounded-xl border border-white/5 bg-card-dark">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-400 font-syne font-bold text-sm"><?= $i + 1 ?></span>
                        </div>
                        <p class="text-sm text-lavender/70 leading-relaxed pt-1"><?= $text ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- PWA Features -->
        <div class="grid grid-cols-3 gap-3 mb-8">
            <?php
            $features = [
                ['zap', 'Instant Launch', 'Opens like a native app'],
                ['wifi-off', 'Offline Shell', 'Works without internet'],
                ['lock', 'Secure', 'Admin-only access'],
            ];
            $feat_colors = ['text-yellow-400 bg-yellow-500/10', 'text-blue-400 bg-blue-500/10', 'text-success-green bg-success-green/10'];
            foreach ($features as $i => [$icon, $title, $desc]):
            ?>
            <div class="bg-card-dark border border-white/5 rounded-xl p-4 text-center">
                <div class="w-9 h-9 rounded-lg <?= $feat_colors[$i] ?> flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="<?= $icon ?>" class="w-4 h-4"></i>
                </div>
                <p class="font-syne font-bold text-white text-xs"><?= $title ?></p>
                <p class="text-lavender/40 text-[10px] mt-1"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Back link -->
        <div class="text-center">
            <a href="/wp/studio-admin.php" class="text-xs text-lavender/40 hover:text-white transition-colors">
                ← Back to Command Center
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Register service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/wp/sw.js', { scope: '/wp/' })
                .then(reg => console.log('SW registered:', reg.scope))
                .catch(err => console.error('SW failed:', err));
        }

        // Handle install prompt
        let deferredPrompt = null;
        const installSection = document.getElementById('install-section');
        const installBtn     = document.getElementById('install-btn');
        const openSection    = document.getElementById('open-section');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installSection.classList.remove('hidden');
            lucide.createIcons();
        });

        installBtn?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                installSection.classList.add('hidden');
                installBtn.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i> Installed!';
                lucide.createIcons();
            }
            deferredPrompt = null;
        });

        window.addEventListener('appinstalled', () => {
            installSection.classList.add('hidden');
            openSection.innerHTML = `
                <div class="w-full py-4 bg-success-green/10 border border-success-green/30 text-success-green font-syne font-bold text-base rounded-xl flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    App Installed Successfully!
                </div>
            `;
        });

        // Detect if already running as standalone PWA
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            document.querySelector('.glow')?.classList.add('opacity-50');
        }
    </script>
</body>
</html>
