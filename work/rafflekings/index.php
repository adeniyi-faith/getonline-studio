<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RaffleKings | GetOnline Studio</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&family=Syne:wght@400;700;800&family=Inter:wght@300;400;500;600;800&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Shared brand theme -->
    <link rel="stylesheet" href="/assets/css/theme.css">
    <script src="/assets/js/theme.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ...GO_COLORS,
                        /* RaffleKings Brand Colors */
                        'rk-purple': '#7c3aed', /* Deep Gaming Purple */
                        'rk-gold': '#eab308', /* Action Gold */
                        'rk-dark': '#0B0B0F',
                        'rk-slate': '#1e293b',
                    },
                    fontFamily: {
                        ...GO_FONTS,
                        'tech-display': ['Space Grotesk', 'sans-serif'], // RaffleKings Branding
                        'system-sans': ['Inter', 'sans-serif'], // RaffleKings Branding
                    },
                    backgroundImage: {
                        'noise': GO_NOISE_BG,
                    },
                    animation: {
                        'spin-slow': 'spin 15s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'void-shift': 'voidShift 10s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        voidShift: {
                            '0%': { backgroundPosition: '0% 50%' },
                            '100%': { backgroundPosition: '100% 50%' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Base body bg/color now lives in /assets/css/theme.css. This page
           keeps its own cursor/reveal-up overrides below since they're
           themed to RaffleKings' purple/gold brand. */

        /* Custom Cursor (Themed to RaffleKings Purple) */
        .cursor-dot, .cursor-outline {
            position: fixed;
            top: 0;
            left: 0;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            z-index: 9999;
            pointer-events: none;
        }
        .cursor-dot { width: 8px; height: 8px; background-color: #eab308; }
        .cursor-outline {
            width: 40px; height: 40px;
            border: 1px solid #7c3aed;
            transition: width 0.2s, height 0.2s, background-color 0.2s;
        }
        @media (pointer: coarse) {
            .cursor-dot, .cursor-outline { display: none; }
            body { cursor: auto; }
        }
        body.hovering .cursor-outline {
            width: 60px; height: 60px;
            background-color: rgba(124, 58, 237, 0.2);
            border-color: transparent;
        }

        /* Reveal Animations */
        .reveal-up {
            opacity: 0;
            transform: translateY(50px);
            animation: fadeUp 1s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        /* Text Stroke */
        .text-stroke {
            -webkit-text-stroke: 1px #e9d5ff;
            color: transparent;
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
<script src="/tracking.js"></script></head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-rk-purple selection:text-white relative">

    <!-- Custom Cursor -->
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <!-- Navigation -->
    <?php $go_accent_class = 'rk-gold'; include $_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'; ?>

    <!-- Hero Section (Immersive Fintech/Gaming Theme) -->
    <header class="relative min-h-[80vh] flex flex-col justify-center items-center px-4 overflow-hidden border-b border-lavender/10">
        <!-- Animated Background Gradient using Deep Gaming Purple & Black -->
        <div class="absolute inset-0 bg-gradient-to-br from-matte-black via-[#0B0B0F] to-[#2e1065] bg-[length:200%_200%] animate-void-shift opacity-90 z-0"></div>
        
        <div class="relative z-10 text-center max-w-4xl pt-20 pb-16 md:pb-24">
            <div class="flex items-center justify-center gap-4 mb-6 reveal-up">
                <span class="px-3 py-1 border border-lavender/30 rounded-full text-[10px] uppercase tracking-widest text-lavender/70">Fintech Web App</span>
                <span class="px-3 py-1 border border-lavender/30 rounded-full text-[10px] uppercase tracking-widest text-lavender/70">PHP / MySQL</span>
            </div>
            
            <h1 class="font-syne text-[10vw] md:text-[8vw] leading-[0.9] font-bold text-lavender mb-4 reveal-up" style="animation-delay: 0.2s;">
                RAFFLEKINGS
            </h1>
            <p class="font-manrope text-rk-gold uppercase tracking-[0.2em] text-xs md:text-sm font-bold reveal-up" style="animation-delay: 0.3s;">Premium Lottery Dashboard</p>

            <!-- Live Site Button -->
            <div class="mt-8 mb-8 reveal-up" style="animation-delay: 0.4s;">
                <a href="#" class="inline-block px-8 py-3 border border-rk-gold/50 rounded-full text-sm font-manrope uppercase tracking-widest hover:bg-rk-gold hover:text-matte-black transition-all duration-300 hover-target">
                    View Live App ↗
                </a>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-t border-lavender/20 pt-8 mt-4 w-full text-left md:text-center gap-8 reveal-up" style="animation-delay: 0.5s;">
                <div>
                    <span class="text-xs uppercase tracking-widest text-lavender/40 block mb-1">Client</span>
                    <span class="font-syne text-lg">RaffleKings</span>
                </div>
                <div>
                    <span class="text-xs uppercase tracking-widest text-lavender/40 block mb-1">Role</span>
                    <span class="font-syne text-lg">Full-Stack Engineering</span>
                </div>
                <div>
                    <span class="text-xs uppercase tracking-widest text-lavender/40 block mb-1">Scope</span>
                    <span class="font-syne text-lg">Backend & UI/UX</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Showcase Image -->
    <section class="px-4 md:px-6 -mt-12 md:-mt-24 relative z-20">
        <div class="max-w-7xl mx-auto reveal-up" style="animation-delay: 0.7s;">
            <!-- Browser Frame -->
            <div class="rounded-t-xl bg-[#1a1a1a] border border-white/10 p-3 flex gap-2 items-center">
                <div class="w-3 h-3 rounded-full bg-red-500/20 border border-red-500/50"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500/20 border border-yellow-500/50"></div>
                <div class="w-3 h-3 rounded-full bg-green-500/20 border border-green-500/50"></div>
                <!-- URL Bar -->
                <div class="ml-4 px-4 py-1 bg-black/50 rounded-md text-[10px] font-mono text-white/30 w-64 truncate">rafflekings.com.ng</div>
            </div>
            <div class="aspect-video w-full overflow-hidden border-x border-b border-white/10 bg-card-dark relative group">
                <!-- User provided screenshot URL -->
                <img src="https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69c056ef61d91271e5147a38.jpg" alt="RaffleKings Web App Preview" class="w-full h-full object-cover object-top transition-transform duration-[3s] group-hover:scale-105">
                
                <!-- Reflection Overlay -->
                <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none"></div>
            </div>
        </div>
    </section>

    <!-- Project Overview -->
    <section class="py-20 md:py-32 px-4 md:px-6">
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="md:col-span-1 reveal-up">
                <h2 class="font-syne text-2xl text-rk-gold mb-4">( THE BRIEF )</h2>
            </div>
            <div class="md:col-span-2 space-y-6 text-lg text-lavender/80 font-manrope reveal-up" style="animation-delay: 0.2s;">
                <p>
                    <strong class="text-white">The Challenge:</strong> The online gaming and lottery space is highly competitive, but often plagued by platforms that look spammy, slow, and untrustworthy. RaffleKings needed a dynamic web application that operated with the lightning speed of a crypto exchange, combined with the secure data management of a real bank.
                </p>
                <p>
                    <strong class="text-white">The Solution:</strong> We engineered a high-performance "Fintech-Gaming" ecosystem. By utilizing a highly customized PHP backend, secure MySQL databases, and asynchronous API endpoints, we built a secure financial engine wrapped in an immersive, latency-free gaming UI. <strong>We bridged the gap between financial security and gaming excitement to drive maximum ticket conversions.</strong>
                </p>
            </div>
        </div>
    </section>

    <!-- Design System Breakdown -->
    <section class="py-20 bg-card-dark border-y border-lavender/10">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="mb-16 text-center reveal-up">
                <h2 class="font-syne text-3xl md:text-5xl mb-4">The Psychology of the Draw</h2>
                <p class="font-manrope text-lavender/50">Designing for trust, FOMO, and immediate action.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- Typography -->
                <div class="glass-card p-8 rounded-2xl reveal-up">
                    <h3 class="font-syne text-xl text-white mb-8 border-b border-white/10 pb-4">Typography</h3>
                    <div class="space-y-8">
                        <div>
                            <span class="text-xs uppercase tracking-widest text-lavender/40 mb-2 block">Headings (The Thrill)</span>
                            <p class="font-tech-display text-5xl font-bold text-white">Space Grotesk</p>
                            <p class="text-sm text-lavender/50 mt-2">A geometric, tech-forward typeface that feels dynamic and modern, perfectly suited for live countdowns and jackpot announcements.</p>
                        </div>
                        <div>
                            <span class="text-xs uppercase tracking-widest text-lavender/40 mb-2 block">UI & Dashboards (The Trust)</span>
                            <p class="font-system-sans text-3xl text-rk-gold">Inter</p>
                            <p class="text-sm text-lavender/50 mt-2">Clean, neutral, and highly legible. It provides the "banking app" feel necessary when users are managing their wallet balances and purchases.</p>
                        </div>
                    </div>
                </div>

                <!-- Color Palette -->
                <div class="glass-card p-8 rounded-2xl reveal-up" style="animation-delay: 0.2s;">
                    <h3 class="font-syne text-xl text-white mb-8 border-b border-white/10 pb-4">Palette</h3>
                    <div class="grid grid-cols-2 gap-4 h-full">
                        <div class="bg-[#0B0B0F] border border-white/10 rounded-lg p-4 flex flex-col justify-end h-32 group hover-target">
                            <span class="font-mono text-xs text-white/50">#0B0B0F</span>
                            <span class="font-syne text-white">Deep Space (Focus)</span>
                        </div>
                        <div class="bg-[#7c3aed] border border-white/10 rounded-lg p-4 flex flex-col justify-end h-32 group hover-target">
                            <span class="font-mono text-xs text-white/50">#7C3AED</span>
                            <span class="font-syne text-white">Neon Purple (Action)</span>
                        </div>
                        <div class="bg-[#eab308] border border-white/10 rounded-lg p-4 flex flex-col justify-end h-32 group hover-target">
                            <span class="font-mono text-xs text-black/50">#EAB308</span>
                            <span class="font-syne text-black">Jackpot Gold (Reward)</span>
                        </div>
                        <div class="bg-[#1e293b] border border-white/10 rounded-lg p-4 flex flex-col justify-end h-32 group hover-target">
                            <span class="font-mono text-xs text-white/50">#1E293B</span>
                            <span class="font-syne text-white">Slate Surface (UI)</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Key Features Grid -->
    <section class="py-20 md:py-32 px-4 md:px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Feature 1 -->
                <div class="p-8 border border-lavender/10 rounded-xl hover:border-rk-gold/50 transition-colors duration-500 group hover-target reveal-up">
                    <div class="w-12 h-12 bg-rk-purple/20 text-rk-gold rounded-full flex items-center justify-center mb-6 border border-rk-purple/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 12L10 12M14 12L18 12M12 6L12 10M12 14L12 18"/><rect width="20" height="14" x="2" y="5" rx="2" ry="2"/></svg>
                    </div>
                    <h3 class="font-syne text-2xl mb-4">Real-Time Thrill</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">
                        We developed a custom PHP API bridge that feeds live ticket sales and countdown data to the frontend, utilizing zero-dependency Vanilla JS to create undeniable urgency (FOMO) during live draws.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 border border-lavender/10 rounded-xl hover:border-rk-gold/50 transition-colors duration-500 group hover-target reveal-up" style="animation-delay: 0.1s;">
                    <div class="w-12 h-12 bg-rk-purple/20 text-rk-gold rounded-full flex items-center justify-center mb-6 border border-rk-purple/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M8 11h8"/></svg>
                    </div>
                    <h3 class="font-syne text-2xl mb-4">Fintech Security</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">
                        Trust is paramount when managing user deposits. We engineered secure MySQL transaction ledgers and implemented banking-grade UI paradigms, including encrypted wallet toggles and modular dashboards.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 border border-lavender/10 rounded-xl hover:border-rk-gold/50 transition-colors duration-500 group hover-target reveal-up" style="animation-delay: 0.2s;">
                    <div class="w-12 h-12 bg-rk-purple/20 text-rk-gold rounded-full flex items-center justify-center mb-6 border border-rk-purple/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m13 2-2 2.5-3-1L7 6l1.5 2L5 9l1 3-3 1.5L4 16l3 1-1.5 2.5L8 21l2-1.5L12 22l2-2.5 3 1L18 18l-1.5-2L20 15l-1-3 3-1.5L21 8l-3-1 1.5-2.5L17 3l-2 1.5Z"/><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                    </div>
                    <h3 class="font-syne text-2xl mb-4">Zero-Latency Rendering</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">
                        In gaming, latency costs money. By pre-loading user wallet data at the server level via PHP, and bypassing bloated frontend frameworks, we ensured the dashboard loads and updates instantaneously.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Tech Stack -->
    <section class="py-20 border-t border-lavender/10 bg-[#050505]">
        <div class="max-w-4xl mx-auto px-4 text-center reveal-up">
            <h4 class="font-mono text-sm text-rk-gold mb-8 uppercase tracking-widest">// THE ENGINE ROOM</h4>
            <div class="flex flex-wrap justify-center gap-4 md:gap-8">
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">Custom PHP API</span>
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">MySQL Database</span>
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">Tailwind CSS UI</span>
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">Vanilla JavaScript</span>
            </div>
        </div>
    </section>

    <!-- NEW PERSUASIVE CTA -->
    <section class="py-24 md:py-32 px-4 md:px-6 text-center bg-matte-black border-t border-lavender/10 relative z-20">
        <div class="max-w-4xl mx-auto reveal-up">
            <h2 class="font-syne text-4xl md:text-7xl mb-6 text-white">Turn users into <span class="text-rk-gold italic">active players.</span></h2>
            <p class="font-manrope text-xl md:text-2xl mb-12 text-lavender/80 leading-relaxed">
                If your web app is slow, clunky, or untrustworthy, your users won't deposit their money. Let us engineer a premium, highly secure digital backend wrapped in an interface that commands absolute trust.
            </p>
            <a href="mailto:hello@getonlinestudio.com" class="inline-block bg-rk-gold text-matte-black px-10 py-4 rounded-full text-lg font-bold uppercase tracking-widest hover:bg-white transition-all hover-target shadow-[0_0_20px_rgba(234,179,8,0.3)]">Engineer Your App</a>
        </div>
    </section>

    <!-- Next Project Nav -->
    <?php
    $go_prev = ['href' => '/work/dekompany', 'label' => 'DeKompany'];
    $go_next = ['href' => '/work/void-walker', 'label' => 'Void Walker'];
    include $_SERVER['DOCUMENT_ROOT'] . '/partials/project-nav.php';
    ?>

    <!-- Footer -->
    <?php $go_accent_class = 'rk-gold'; include $_SERVER['DOCUMENT_ROOT'] . '/partials/footer.php'; ?>

    <script>
        // High Performance Cursor
        const cursorDot = document.querySelector('.cursor-dot');
        const cursorOutline = document.querySelector('.cursor-outline');
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        if (!isTouchDevice) {
            let mouseX = 0, mouseY = 0, outlineX = 0, outlineY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                cursorDot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
            });
            const animateCursor = () => {
                outlineX += (mouseX - outlineX) * 0.15;
                outlineY += (mouseY - outlineY) * 0.15;
                cursorOutline.style.transform = `translate(${outlineX}px, ${outlineY}px) translate(-50%, -50%)`;
                requestAnimationFrame(animateCursor);
            };
            animateCursor();
            const hoverTargets = document.querySelectorAll('.hover-target');
            hoverTargets.forEach(el => {
                el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
            });
        }
    </script>
</body>
</html>