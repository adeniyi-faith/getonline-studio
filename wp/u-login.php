<?php
/**
 * GETONLINE STUDIO - CUSTOM SECURE LOGIN
 * Beautiful, mobile-optimized entry point to the pSEO Command Center.
 */

// 1. Boot up WordPress silently
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

// 2. Secure Redirect Handling
$redirect_to = isset($_REQUEST['redirect_to']) ? esc_url_raw($_REQUEST['redirect_to']) : 'https://getonlinestudio.com/wp/studio-admin.php';
$error_message = '';

// If already logged in and has admin rights, send them straight through
if (is_user_logged_in() && current_user_can('manage_options')) {
    wp_safe_redirect($redirect_to);
    exit;
}

// 3. Process Login Attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log'], $_POST['pwd'])) {
    $creds = array(
        'user_login'    => sanitize_user($_POST['log']),
        'user_password' => $_POST['pwd'],
        'remember'      => isset($_POST['rememberme'])
    );

    // Authenticate via WordPress Core
    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        // Strip out WP's default HTML links to keep the UI clean
        $error_message = wp_strip_all_tags($user->get_error_message());
    } else {
        // Success! Double check they are an admin before letting them into the dashboard
        if ($user->has_cap('manage_options')) {
            wp_safe_redirect($redirect_to);
            exit;
        } else {
            wp_logout();
            $error_message = "Your account does not have Command Center privileges.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | pSEO Command Center</title>
    
    <!-- Favicon -->
    <link rel="icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" sizes="32x32" />
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg" />

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
        body { 
            background-color: #0a0a0a; 
            color: #e9d5ff; 
            font-family: 'Manrope', sans-serif;
            background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E');
        }
        
        /* Utility to ensure iOS doesn't zoom inputs */
        input { font-size: 16px !important; }
        @media (min-width: 768px) { input { font-size: 14px !important; } }
        
        .glass-panel {
            background: rgba(18, 18, 18, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-[100dvh] relative overflow-hidden selection:bg-sharp-purple selection:text-white px-4">

    <!-- Ambient Background Glows -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sharp-purple/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto bg-card-dark border border-white/10 rounded-2xl flex items-center justify-center mb-4 shadow-[0_0_30px_rgba(126,34,206,0.15)]">
                <span class="font-syne font-bold text-2xl text-white tracking-wider">GO<span class="text-sharp-purple">.</span></span>
            </div>
            <h1 class="font-syne text-2xl md:text-3xl font-bold text-white mb-2">Command Center</h1>
            <p class="text-sm text-lavender/50">Authenticate to access the pSEO engine.</p>
        </div>

        <!-- Login Card -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 shadow-2xl relative overflow-hidden">
            
            <!-- Top Accent Line -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-sharp-purple via-blue-500 to-sharp-purple"></div>

            <?php if ($error_message): ?>
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl flex items-start gap-3 animate-[slideInUp_0.3s_ease]">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm font-medium text-red-400 leading-snug"><?= esc_html($error_message) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="login-form" class="space-y-5 m-0">
                <input type="hidden" name="redirect_to" value="<?= esc_attr($redirect_to) ?>">

                <div>
                    <label for="log" class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2 font-bold">Username or Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-4 h-4 text-lavender/40"></i>
                        </div>
                        <input type="text" name="log" id="log" required autofocus
                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-white placeholder-lavender/20 focus:border-sharp-purple focus:ring-1 focus:ring-sharp-purple outline-none transition-all"
                            placeholder="admin@getonlinestudio.com">
                    </div>
                </div>

                <div>
                    <label for="pwd" class="block text-[10px] uppercase tracking-widest text-lavender/50 mb-2 font-bold">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-4 h-4 text-lavender/40"></i>
                        </div>
                        <input type="password" name="pwd" id="pwd" required
                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl pl-11 pr-12 py-3.5 text-white placeholder-lavender/20 focus:border-sharp-purple focus:ring-1 focus:ring-sharp-purple outline-none transition-all"
                            placeholder="••••••••••••">
                        <button type="button" id="toggle-pwd" class="absolute inset-y-0 right-0 pr-4 flex items-center text-lavender/40 hover:text-white transition-colors focus:outline-none">
                            <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="rememberme" id="rememberme" class="peer sr-only">
                            <div class="w-4 h-4 rounded border border-white/20 bg-[#0a0a0a] peer-checked:bg-sharp-purple peer-checked:border-sharp-purple transition-all flex items-center justify-center group-hover:border-sharp-purple/50">
                                <i data-lucide="check" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <span class="text-xs text-lavender/60 group-hover:text-lavender transition-colors">Keep me signed in</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submit-btn" class="w-full bg-sharp-purple text-white rounded-xl py-4 text-sm font-bold shadow-[0_0_20px_rgba(126,34,206,0.25)] hover:bg-white hover:text-matte-black transition-all flex items-center justify-center gap-2 group">
                        <span id="btn-text">Secure Login</span>
                        <i data-lucide="arrow-right" id="btn-icon" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        <div id="btn-spinner" class="hidden w-4 h-4 rounded-full border-2 border-white/30 border-t-white animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-[10px] font-mono text-lavender/30 uppercase tracking-widest">
                <i data-lucide="shield-check" class="w-3 h-3 inline-block mr-1 align-text-bottom"></i> 
                Protected by pSEO Engine Core
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Password visibility toggle
        const togglePwdBtn = document.getElementById('toggle-pwd');
        const pwdInput = document.getElementById('pwd');
        const eyeIcon = document.getElementById('eye-icon');

        togglePwdBtn.addEventListener('click', () => {
            const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
            pwdInput.setAttribute('type', type);
            
            // Swap icon using Lucide syntax
            if (type === 'text') {
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        });

        // Loading state on submit
        const loginForm = document.getElementById('login-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');
        const btnSpinner = document.getElementById('btn-spinner');

        loginForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            btnText.textContent = 'Authenticating...';
            btnIcon.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
        });
    </script>
</body>
</html>