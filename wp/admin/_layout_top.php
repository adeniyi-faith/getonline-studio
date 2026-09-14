<?php
/**
 * Shared head + sidebar for every /wp/admin/ screen.
 * Expects (optionally) before including:
 *   $page_title — string shown in <title> and the page header
 *   $active     — one of: dashboard, portfolio, pages, settings
 *   $admin_message — string, shown as a success banner if set
 *   $admin_error   — string, shown as an error banner if set
 */
$page_title = $page_title ?? 'Admin';
$active = $active ?? '';

$go_nav_links = [
    'dashboard' => ['href' => '/wp/admin/index.php', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
    'portfolio' => ['href' => '/wp/admin/portfolio.php', 'label' => 'Portfolio', 'icon' => 'briefcase'],
    'pages'     => ['href' => '/wp/admin/pages.php', 'label' => 'Site Pages', 'icon' => 'file-text'],
    'settings'  => ['href' => '/wp/admin/settings.php', 'label' => 'Navigation & Settings', 'icon' => 'sliders-horizontal'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($page_title); ?> | GetOnline Studio Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <script src="/assets/js/theme.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { ...GO_COLORS },
                    fontFamily: { ...GO_FONTS },
                }
            }
        }
    </script>
    <style>
        body { cursor: auto; }
        .admin-sidebar-link.active { background: rgba(126,34,206,0.15); color: #e9d5ff; border-color: rgba(126,34,206,0.4); }
        .go-input {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(233,213,255,0.15);
            border-radius: 0.5rem;
            padding: 0.6rem 0.9rem;
            color: #e9d5ff;
            font-family: 'Manrope', sans-serif;
            font-size: 0.9rem;
        }
        .go-input:focus { outline: none; border-color: #7e22ce; }
        .go-label { display: block; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(233,213,255,0.5); margin-bottom: 0.35rem; }
    </style>
</head>
<body class="bg-matte-black font-manrope text-lavender min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 shrink-0 border-r border-lavender/10 bg-[#0a0a0a] min-h-screen hidden md:flex flex-col">
        <div class="px-6 py-6 border-b border-lavender/10">
            <a href="/" class="font-syne font-bold text-xl hover:text-sharp-purple transition-colors">GO.</a>
            <div class="text-xs text-lavender/40 uppercase tracking-widest mt-1">Command Center</div>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1">
            <?php foreach ($go_nav_links as $key => $link): ?>
                <a href="<?php echo htmlspecialchars($link['href']); ?>"
                   class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm border border-transparent text-lavender/70 hover:text-lavender hover:bg-white/5 transition-colors <?php echo $active === $key ? 'active' : ''; ?>">
                    <i data-lucide="<?php echo htmlspecialchars($link['icon']); ?>" class="w-4 h-4"></i>
                    <?php echo htmlspecialchars($link['label']); ?>
                </a>
            <?php endforeach; ?>
            <div class="pt-3 mt-3 border-t border-lavender/10">
                <a href="/wp/studio-admin.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-lavender/50 hover:text-lavender hover:bg-white/5 transition-colors">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    pSEO Command Center
                </a>
            </div>
        </nav>
        <div class="px-6 py-4 border-t border-lavender/10 text-xs text-lavender/40">
            Signed in as <?php echo htmlspecialchars(wp_get_current_user()->user_login ?? ''); ?>
            <br>
            <a href="<?php echo htmlspecialchars(wp_logout_url('/wp/u-login.php')); ?>" class="text-sharp-purple hover:text-white transition-colors">Log out</a>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 min-w-0">
        <header class="border-b border-lavender/10 px-6 md:px-10 py-6 flex items-center justify-between">
            <h1 class="font-syne text-2xl md:text-3xl font-bold text-lavender"><?php echo htmlspecialchars($page_title); ?></h1>
            <a href="/" target="_blank" class="text-xs uppercase tracking-widest border border-lavender/30 px-4 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all">View Site</a>
        </header>
        <div class="px-6 md:px-10 py-8">
            <?php if (!empty($admin_message)): ?>
                <div class="mb-6 px-4 py-3 rounded-lg border border-green-500/30 bg-green-500/10 text-green-300 text-sm">
                    <?php echo htmlspecialchars($admin_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($admin_error)): ?>
                <div class="mb-6 px-4 py-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-300 text-sm">
                    <?php echo htmlspecialchars($admin_error); ?>
                </div>
            <?php endif; ?>
