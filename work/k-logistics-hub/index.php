<?php
// Draws one app screenshot inside a phone frame.
function kl_phone($file, $alt, $extra = '') {
    $src = '/work/k-logistics-hub/img/' . $file . '.webp';
    echo '<div class="kl-phone ' . htmlspecialchars($extra) . '">'
       . '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '" width="585" height="1266" decoding="async">'
       . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-logistics Hub Case Study | GetOnline Studio</title>
    <meta name="description" content="How GetOnline Studio replaced paper, phone calls and guesswork at K-logistics Hub Ltd, Port Harcourt, with one mobile app for orders, deliveries, stock and money, plus an AI business assistant.">
    <link rel="canonical" href="https://getonlinestudio.com/work/k-logistics-hub/">
    <meta property="og:title" content="K-logistics Hub: one app that runs the whole business">
    <meta property="og:description" content="From paper and phone calls to one app for the owner, office staff and dispatch riders.">
    <meta property="og:image" content="https://getonlinestudio.com/work/k-logistics-hub/img/admin-home.webp">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">

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
                        /* K-logistics Hub brand colours */
                        'kl-navy': '#13203d',
                        'kl-deep': '#0b1428',
                        'kl-orange': '#ea7c0a',
                        'kl-green': '#4ade80',
                    },
                    fontFamily: { ...GO_FONTS },
                    backgroundImage: {
                        'noise': GO_NOISE_BG,
                    },
                    animation: {
                        'float': 'float 7s ease-in-out infinite',
                        'void-shift': 'voidShift 12s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-14px)' }
                        },
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
        /* Cursor themed to K-logistics orange/navy */
        .cursor-dot, .cursor-outline {
            position: fixed; top: 0; left: 0;
            transform: translate(-50%, -50%);
            border-radius: 50%; z-index: 9999; pointer-events: none;
        }
        .cursor-dot { width: 8px; height: 8px; background-color: #ea7c0a; }
        .cursor-outline {
            width: 40px; height: 40px;
            border: 1px solid rgba(234, 124, 10, 0.6);
            transition: width 0.2s, height 0.2s, background-color 0.2s;
        }
        @media (pointer: coarse) {
            .cursor-dot, .cursor-outline { display: none; }
            body { cursor: auto; }
        }
        body.hovering .cursor-outline {
            width: 60px; height: 60px;
            background-color: rgba(234, 124, 10, 0.15);
            border-color: transparent;
        }

        .reveal-up {
            opacity: 0;
            transform: translateY(50px);
            animation: fadeUp 1s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        /* Sections lower down fade in as they scroll into view */
        .on-scroll { opacity: 0; transform: translateY(40px); transition: opacity 0.9s cubic-bezier(0.25, 1, 0.5, 1), transform 0.9s cubic-bezier(0.25, 1, 0.5, 1); }
        .on-scroll.is-visible { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) {
            .reveal-up, .on-scroll { opacity: 1 !important; transform: none !important; animation: none !important; transition: none !important; }
            .animate-float { animation: none !important; }
        }

        .text-stroke-orange { -webkit-text-stroke: 1px #ea7c0a; color: transparent; }

        /* Phone frame around each app screenshot */
        .kl-phone {
            position: relative;
            border-radius: 2.4rem;
            padding: 0.55rem;
            background: linear-gradient(145deg, #2a2f3d, #0d0f15);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 30px 60px -20px rgba(0,0,0,0.8), 0 0 80px -30px rgba(234,124,10,0.35);
        }
        .kl-phone img {
            display: block; width: 100%; height: auto;
            border-radius: 1.9rem;
            background: #f3f4f7;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Sideways-scrolling strip of screens on small phones */
        .kl-strip { scroll-snap-type: x mandatory; scrollbar-width: none; }
        .kl-strip::-webkit-scrollbar { display: none; }
        .kl-strip > * { scroll-snap-align: center; }
    </style>
<script src="/tracking.js"></script></head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-kl-orange selection:text-white relative overflow-x-hidden">

    <!-- Custom Cursor -->
    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <!-- Navigation -->
    <?php $go_accent_class = 'kl-orange'; include $_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'; ?>

    <!-- ═══════════ HERO ═══════════ -->
    <header class="relative overflow-hidden border-b border-lavender/10">
        <div class="absolute inset-0 bg-gradient-to-br from-matte-black via-kl-deep to-[#3a1d05] bg-[length:200%_200%] animate-void-shift opacity-90"></div>
        <div class="absolute -top-40 -right-40 w-[38rem] h-[38rem] rounded-full bg-kl-orange/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 md:px-8 pt-32 md:pt-40 pb-20 md:pb-28 grid grid-cols-1 lg:grid-cols-12 gap-14 items-center">
            <div class="lg:col-span-6">
                <div class="flex flex-wrap items-center gap-3 mb-6 reveal-up">
                    <span class="px-3 py-1 bg-kl-orange text-matte-black rounded-full text-[10px] font-bold uppercase tracking-widest">New case study</span>
                    <span class="px-3 py-1 border border-lavender/30 rounded-full text-[10px] uppercase tracking-widest text-lavender/70">Mobile App</span>
                    <span class="px-3 py-1 border border-lavender/30 rounded-full text-[10px] uppercase tracking-widest text-lavender/70">AI Assistant</span>
                </div>
                <h1 class="font-syne text-[11vw] sm:text-6xl xl:text-7xl leading-[0.9] font-bold text-lavender mb-6 whitespace-nowrap reveal-up" style="animation-delay: 0.15s;">
                    K&#8209;LOGISTICS<br><span class="text-stroke-orange">HUB</span>
                </h1>
                <p class="font-syne text-2xl md:text-3xl text-white leading-snug mb-6 max-w-xl reveal-up" style="animation-delay: 0.25s;">
                    From paper and phone calls to <span class="text-kl-orange">one app that runs the whole business.</span>
                </p>
                <p class="text-lavender/70 text-base md:text-lg leading-relaxed max-w-xl reveal-up" style="animation-delay: 0.35s;">
                    A mobile app for the owner, office staff and dispatch riders of a Port Harcourt health products distributor, with an AI assistant that answers business questions in plain English.
                </p>

                <dl class="grid grid-cols-2 gap-x-8 gap-y-6 border-t border-lavender/20 pt-8 mt-10 reveal-up" style="animation-delay: 0.45s;">
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-lavender/40 mb-1">Client</dt>
                        <dd class="font-syne text-lg">K-logistics Hub Ltd, Port Harcourt</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-lavender/40 mb-1">Industry</dt>
                        <dd class="font-syne text-lg">Health products &amp; last-mile delivery</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-lavender/40 mb-1">What we built</dt>
                        <dd class="font-syne text-lg">Mobile app + AI business assistant</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-lavender/40 mb-1">Timeline</dt>
                        <dd class="font-syne text-lg text-kl-orange">Design to first version in two days</dd>
                    </div>
                </dl>
            </div>

            <!-- Three phones fanned out -->
            <div class="lg:col-span-6 relative h-[480px] sm:h-[560px] md:h-[640px] reveal-up" style="animation-delay: 0.5s;">
                <div class="absolute left-1/2 top-1/2 -translate-x-[115%] -translate-y-[44%] w-[38%] max-w-[230px] -rotate-[8deg] opacity-80">
                    <?php kl_phone('rider-home', "Rider's home screen showing the next delivery and cash to collect"); ?>
                </div>
                <div class="absolute left-1/2 top-1/2 translate-x-[15%] -translate-y-[44%] w-[38%] max-w-[230px] rotate-[8deg] opacity-80">
                    <?php kl_phone('ai-assistant', 'AI assistant answering a question about profit and low stock'); ?>
                </div>
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[44%] max-w-[270px] z-10">
                    <div class="animate-float">
                        <?php kl_phone('admin-home', "Owner's home screen showing today's orders, cost of goods, expenses and profit"); ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ═══════════ AT A GLANCE NUMBERS ═══════════ -->
    <section class="border-b border-lavender/10 bg-card-dark">
        <div class="max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-2 md:grid-cols-4">
            <div class="py-10 md:py-14 px-2 md:px-6 border-r border-lavender/10 on-scroll">
                <p class="font-syne text-4xl md:text-6xl font-bold text-white">3</p>
                <p class="text-sm text-lavender/60 mt-2">apps in one: owner, office staff and riders</p>
            </div>
            <div class="py-10 md:py-14 px-2 md:px-6 md:border-r border-lavender/10 on-scroll">
                <p class="font-syne text-4xl md:text-6xl font-bold text-kl-orange">2 days</p>
                <p class="text-sm text-lavender/60 mt-2">from design to first working version</p>
            </div>
            <div class="py-10 md:py-14 px-2 md:px-6 border-r border-t md:border-t-0 border-lavender/10 on-scroll">
                <p class="font-syne text-4xl md:text-6xl font-bold text-white">100%</p>
                <p class="text-sm text-lavender/60 mt-2">of orders, deliveries and naira recorded</p>
            </div>
            <div class="py-10 md:py-14 px-2 md:px-6 border-t md:border-t-0 border-lavender/10 on-scroll">
                <p class="font-syne text-4xl md:text-6xl font-bold text-white">Offline</p>
                <p class="text-sm text-lavender/60 mt-2">works with no network, syncs when it returns</p>
            </div>
        </div>
    </section>

    <!-- ═══════════ THE CHALLENGE ═══════════ -->
    <section class="py-20 md:py-32 px-4 md:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 mb-14">
                <div class="md:col-span-4 on-scroll">
                    <h2 class="font-syne text-2xl text-kl-orange">( THE CHALLENGE )</h2>
                </div>
                <div class="md:col-span-8 space-y-6 text-lg md:text-xl text-lavender/80 leading-relaxed on-scroll">
                    <p>K-logistics Hub Ltd sells herbal and health products to shops, chemists and individual buyers across Port Harcourt, and delivers them with its own team of motorbike riders.</p>
                    <p><strong class="text-white">As the business grew, the way it was run didn't keep up.</strong> Almost everything was done by hand.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-8 md:p-10 border border-lavender/10 rounded-2xl hover:border-kl-orange/50 transition-colors duration-500 on-scroll">
                    <span class="font-mono text-xs text-kl-orange">01</span>
                    <h3 class="font-syne text-2xl text-white mt-3 mb-4">Orders lived in notebooks and WhatsApp chats.</h3>
                    <p class="text-lavender/60 leading-relaxed">A customer would call, someone would write the order down, and a rider would be told by phone. When a customer later said "my order never came", there was no easy way to check who took it, which rider had it, or who received it.</p>
                </div>
                <div class="p-8 md:p-10 border border-lavender/10 rounded-2xl hover:border-kl-orange/50 transition-colors duration-500 on-scroll">
                    <span class="font-mono text-xs text-kl-orange">02</span>
                    <h3 class="font-syne text-2xl text-white mt-3 mb-4">Stock was counted by hand, when someone had the time.</h3>
                    <p class="text-lavender/60 leading-relaxed">Nobody could say for sure how many boxes of a product were on the shelf. Items ran out without warning, and damaged or expired goods went unrecorded.</p>
                </div>
                <div class="p-8 md:p-10 border border-lavender/10 rounded-2xl hover:border-kl-orange/50 transition-colors duration-500 on-scroll">
                    <span class="font-mono text-xs text-kl-orange">03</span>
                    <h3 class="font-syne text-2xl text-white mt-3 mb-4">Money went unaccounted for.</h3>
                    <p class="text-lavender/60 leading-relaxed">Riders collected cash on delivery, expenses were paid out of pocket, and staff and riders were paid from memory and loose receipts. At the end of the day the figures rarely matched, and there was no clear answer to the simplest question an owner can ask: <em class="text-white">"Did we make a profit today?"</em></p>
                </div>
                <div class="p-8 md:p-10 border border-lavender/10 rounded-2xl hover:border-kl-orange/50 transition-colors duration-500 on-scroll">
                    <span class="font-mono text-xs text-kl-orange">04</span>
                    <h3 class="font-syne text-2xl text-white mt-3 mb-4">Everything went through the owner.</h3>
                    <p class="text-lavender/60 leading-relaxed">Staff had to ask before doing almost anything, because there was no safe way to give each person just the access they needed. That made the owner the bottleneck for every decision.</p>
                </div>
            </div>

            <p class="max-w-3xl mx-auto text-center text-lavender/60 text-lg leading-relaxed mt-14 on-scroll">The records weren't complete, mistakes were hard to trace, and the owner spent his days chasing information instead of growing the business.</p>
        </div>
    </section>

    <!-- ═══════════ THE GOAL ═══════════ -->
    <section class="relative py-24 md:py-36 px-4 md:px-8 bg-kl-navy overflow-hidden">
        <div class="absolute -bottom-32 -left-32 w-[30rem] h-[30rem] rounded-full bg-kl-orange/15 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-5xl mx-auto text-center on-scroll">
            <h2 class="font-mono text-xs text-kl-orange uppercase tracking-widest mb-8">// The goal</h2>
            <p class="font-syne text-3xl md:text-5xl leading-tight text-white">
                One place where <span class="text-kl-orange">every order, delivery, product and naira</span> is recorded the moment it happens, by the person doing it.
            </p>
            <p class="text-lavender/70 text-lg md:text-xl leading-relaxed mt-10 max-w-3xl mx-auto">So the owner can see the true state of the business at any time from his phone. And it had to work in real conditions: riders on the move, patchy mobile network, and busy people with no time to learn complicated software.</p>
        </div>
    </section>

    <!-- ═══════════ THE SOLUTION INTRO ═══════════ -->
    <section class="pt-20 md:pt-32 px-4 md:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-10">
            <div class="md:col-span-4 on-scroll">
                <h2 class="font-syne text-2xl text-kl-orange">( THE SOLUTION )</h2>
            </div>
            <div class="md:col-span-8 on-scroll">
                <p class="font-syne text-3xl md:text-5xl text-white leading-tight">One app. <span class="text-lavender/50">A different home screen for each kind of user.</span></p>
            </div>
        </div>
    </section>

    <!-- For the owner -->
    <section class="py-20 md:py-28 px-4 md:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-20 items-center">
            <div class="on-scroll">
                <span class="inline-block font-mono text-[10px] uppercase tracking-widest text-matte-black bg-kl-orange px-3 py-1 rounded-full mb-6">For the owner (Admin)</span>
                <h3 class="font-syne text-3xl md:text-5xl text-white leading-tight mb-8">The whole day at a glance.</h3>
                <ul class="space-y-5 text-lavender/70 leading-relaxed">
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Today's orders, cost of goods, expenses and profit</strong> the moment you open the app.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Full control of the team:</strong> add staff and riders, create their sign-in details, choose exactly what each person can see and do, and block a rider from signing in.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">"See the app as…"</strong> lets the owner view the app exactly as any staff member or rider sees it.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Close the day:</strong> a daily record of stock and money, so every morning starts with the right figures.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Reports</strong> downloadable as PDF or Excel.</span></li>
                </ul>
            </div>
            <div class="flex justify-center gap-5 md:gap-8 on-scroll">
                <div class="w-[46%] max-w-[260px]"><?php kl_phone('admin-home', "Owner's home screen: today's orders worth ₦645,000 and a profit of ₦185,000"); ?></div>
                <div class="w-[46%] max-w-[260px] mt-16"><?php kl_phone('finance', 'Finance screen showing profit for the week and orders each day'); ?></div>
            </div>
        </div>
    </section>

    <!-- For the office staff -->
    <section class="py-20 md:py-28 px-4 md:px-8 bg-card-dark border-y border-lavender/10">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-20 items-center">
            <div class="lg:order-2 on-scroll">
                <span class="inline-block font-mono text-[10px] uppercase tracking-widest text-matte-black bg-lavender px-3 py-1 rounded-full mb-6">For the office staff</span>
                <h3 class="font-syne text-3xl md:text-5xl text-white leading-tight mb-8">Orders and stock, handled in seconds.</h3>
                <ul class="space-y-5 text-lavender/70 leading-relaxed">
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Take orders in seconds:</strong> pick the customer, the products and the rider. The price and stock are checked automatically.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Receive and count stock</strong> with a reason for every change (damaged, expired, returned, recount), so every number can be traced.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Find any order instantly</strong> by its order number, customer name, phone number or product.</span></li>
                </ul>
            </div>
            <div class="lg:order-1 kl-strip flex lg:justify-center gap-5 md:gap-6 overflow-x-auto lg:overflow-visible -mx-4 px-4 pb-4 lg:mx-0 lg:px-0 on-scroll">
                <div class="shrink-0 w-[62%] sm:w-[40%] lg:w-[31%] max-w-[240px]"><?php kl_phone('staff-home', "Staff home screen: today's deliveries, stock levels and riders"); ?></div>
                <div class="shrink-0 w-[62%] sm:w-[40%] lg:w-[31%] max-w-[240px] lg:mt-12"><?php kl_phone('orders-today', "Today's orders and deliveries, filterable by rider"); ?></div>
                <div class="shrink-0 w-[62%] sm:w-[40%] lg:w-[31%] max-w-[240px] lg:mt-24"><?php kl_phone('stock', 'Stock screen showing what is in store and what is running low'); ?></div>
            </div>
        </div>
    </section>

    <!-- For the riders -->
    <section class="py-20 md:py-28 px-4 md:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-20 items-center">
            <div class="on-scroll">
                <span class="inline-block font-mono text-[10px] uppercase tracking-widest text-white bg-kl-navy border border-kl-orange/40 px-3 py-1 rounded-full mb-6">For the riders</span>
                <h3 class="font-syne text-3xl md:text-5xl text-white leading-tight mb-8">Every delivery proven, every naira traced.</h3>
                <ul class="space-y-5 text-lavender/70 leading-relaxed">
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Today's deliveries on their own phone:</strong> where to go, what to deliver, and how much cash to collect.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Proof of delivery:</strong> each delivery is confirmed with the receiver's name, and a photo if the owner turns that on. Every confirmation records the time and the rider.</span></li>
                    <li class="flex gap-4"><span class="text-kl-orange mt-1">✦</span><span><strong class="text-white">Their own earnings</strong>, so pay is clear and fair.</span></li>
                </ul>
            </div>
            <div class="kl-strip flex lg:justify-center gap-5 md:gap-6 overflow-x-auto lg:overflow-visible -mx-4 px-4 pb-4 lg:mx-0 lg:px-0 on-scroll">
                <div class="shrink-0 w-[62%] sm:w-[40%] lg:w-[31%] max-w-[240px]"><?php kl_phone('rider-home', "Rider's home screen: next delivery, cash to collect and money earned today"); ?></div>
                <div class="shrink-0 w-[62%] sm:w-[40%] lg:w-[31%] max-w-[240px] lg:mt-12"><?php kl_phone('order-details', 'Order details with options to change it, mark it delivered or record a failed delivery'); ?></div>
                <div class="shrink-0 w-[62%] sm:w-[40%] lg:w-[31%] max-w-[240px] lg:mt-24"><?php kl_phone('delivery-proof', 'Delivery proof showing the time, the rider and who received the order'); ?></div>
            </div>
        </div>
    </section>

    <!-- ═══════════ BUILT FOR REAL CONDITIONS ═══════════ -->
    <section class="py-20 md:py-28 px-4 md:px-8 bg-[#050505] border-y border-lavender/10">
        <div class="max-w-7xl mx-auto">
            <div class="mb-14 text-center on-scroll">
                <h2 class="font-syne text-3xl md:text-5xl mb-4">Built for real conditions.</h2>
                <p class="text-lavender/50">Riders in low-signal areas. Two phones editing the same order. Staff who shouldn't see the profit.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card p-8 rounded-2xl hover:border-kl-orange/40 transition-colors duration-500 on-scroll">
                    <div class="w-12 h-12 bg-kl-orange/15 text-kl-orange rounded-full flex items-center justify-center mb-6 border border-kl-orange/40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h.01"/><path d="M8.5 16.43a5 5 0 0 1 7 0"/><path d="M5 12.86a10 10 0 0 1 5.17-2.69"/><path d="M19 12.86a10 10 0 0 0-2-1.43"/><path d="M2 8.82a15 15 0 0 1 4.18-2.65"/><path d="M22 8.82a15 15 0 0 0-11.29-3.76"/><path d="m2 2 20 20"/></svg>
                    </div>
                    <h3 class="font-syne text-2xl mb-4">Works offline</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">Everything is saved on the phone first and sent to the cloud when the network returns, so a rider in a low-signal area never loses a delivery record.</p>
                </div>
                <div class="glass-card p-8 rounded-2xl hover:border-kl-orange/40 transition-colors duration-500 on-scroll">
                    <div class="w-12 h-12 bg-kl-orange/15 text-kl-orange rounded-full flex items-center justify-center mb-6 border border-kl-orange/40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><path d="M12 18h.01"/><path d="m9 9 2 2 4-4"/></svg>
                    </div>
                    <h3 class="font-syne text-2xl mb-4">No lost or doubled records</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">If two phones change the same thing, the owner chooses which version to keep. If the server refuses a change, the app lists it instead of letting it disappear.</p>
                </div>
                <div class="glass-card p-8 rounded-2xl hover:border-kl-orange/40 transition-colors duration-500 on-scroll">
                    <div class="w-12 h-12 bg-kl-orange/15 text-kl-orange rounded-full flex items-center justify-center mb-6 border border-kl-orange/40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><rect width="6" height="5" x="9" y="11" rx="1"/><path d="M10 11V9a2 2 0 1 1 4 0v2"/></svg>
                    </div>
                    <h3 class="font-syne text-2xl mb-4">Secure by design</h3>
                    <p class="text-sm text-lavender/60 leading-relaxed">Access rules are enforced on the server, not just hidden in the app, so a rider can never see cost prices or the business's profit.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ AI ASSISTANT ═══════════ -->
    <section class="relative py-20 md:py-32 px-4 md:px-8 overflow-hidden">
        <div class="absolute top-1/2 right-0 -translate-y-1/2 w-[40rem] h-[40rem] rounded-full bg-kl-orange/10 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-20 items-center">
            <div class="flex justify-center on-scroll">
                <div class="w-[68%] max-w-[300px] animate-float"><?php kl_phone('ai-assistant', 'AI assistant answering "Which products are running low?" with a list from live stock'); ?></div>
            </div>
            <div class="on-scroll">
                <span class="inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-widest text-kl-orange border border-kl-orange/40 px-3 py-1 rounded-full mb-6">✦ An AI business assistant</span>
                <h2 class="font-syne text-4xl md:text-6xl text-white leading-[1.05] mb-6">Ask the business a question. <span class="text-kl-orange">Get the answer in seconds.</span></h2>
                <p class="text-lavender/70 text-lg leading-relaxed mb-10">The owner simply asks, in plain English. The assistant answers using the business's own live figures, with a link straight to the right screen.</p>
                <div class="space-y-4">
                    <div class="glass-card rounded-2xl rounded-br-sm px-6 py-4 ml-auto max-w-sm text-white font-syne text-lg">"What is our profit this week?"</div>
                    <div class="glass-card rounded-2xl rounded-br-sm px-6 py-4 ml-auto max-w-sm text-white font-syne text-lg">"Which products are running low?"</div>
                    <div class="glass-card rounded-2xl rounded-br-sm px-6 py-4 ml-auto max-w-sm text-white font-syne text-lg">"Which rider delivered the most?"</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════ RESULTS ═══════════ -->
    <section class="py-20 md:py-32 px-4 md:px-8 bg-card-dark border-y border-lavender/10">
        <div class="max-w-6xl mx-auto">
            <div class="mb-14 on-scroll">
                <h2 class="font-syne text-2xl text-kl-orange mb-6">( THE RESULTS )</h2>
                <p class="font-syne text-3xl md:text-5xl text-white leading-tight max-w-4xl">The owner now sees his whole business from his phone, and his team can work without waiting on him.</p>
            </div>

            <div class="hidden md:grid grid-cols-2 gap-6 px-8 pb-4 font-mono text-xs uppercase tracking-widest">
                <span class="text-lavender/40">Before</span>
                <span class="text-kl-orange">After</span>
            </div>
            <div class="space-y-3">
                <?php
                $kl_results = [
                    ['Orders scattered across notebooks and WhatsApp', 'Every order recorded, numbered (e.g. ORD-1002-19) and searchable'],
                    ['"My order never came", with no proof either way', 'Every delivery shows who delivered it, when, and who received it'],
                    ['Stock counted by hand, items running out unexpectedly', 'Live stock levels, low-stock alerts and a reason for every stock change'],
                    ['Cash and expenses hard to account for', 'Every naira in and out recorded, with daily profit worked out automatically'],
                    ['Staff waiting on the owner for everything', 'Each person has the access they need, and no more'],
                    ["End-of-day figures that didn't add up", 'A daily closing record and downloadable reports'],
                    ['Questions answered by digging through records', 'Answers in seconds from the AI assistant'],
                ];
                foreach ($kl_results as $kl_row): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 rounded-2xl overflow-hidden border border-lavender/10 on-scroll">
                    <div class="px-6 md:px-8 py-5 bg-matte-black text-lavender/50">
                        <span class="md:hidden block font-mono text-[10px] uppercase tracking-widest text-lavender/30 mb-1">Before</span>
                        <span class="line-through decoration-lavender/30"><?php echo htmlspecialchars($kl_row[0]); ?></span>
                    </div>
                    <div class="px-6 md:px-8 py-5 bg-kl-navy text-white flex gap-3">
                        <span class="text-kl-orange shrink-0">✓</span>
                        <span><?php echo htmlspecialchars($kl_row[1]); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════ HOW WE DELIVERED IT ═══════════ -->
    <section class="py-20 md:py-32 px-4 md:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-14 text-center on-scroll">
                <h2 class="font-syne text-3xl md:text-5xl mb-4">How we delivered it</h2>
                <p class="text-lavender/50">Design to first version in two days.</p>
            </div>
            <ol class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <?php
                $kl_steps = [
                    ['Understand the business first', 'We mapped how orders, stock, deliveries and money actually moved through the company, and who needed to see what.'],
                    ['Design before code', "Every screen was designed in the company's brand colours (navy and orange) before building began, which made the build fast."],
                    ['Build in clear steps', 'Sign-in and offline saving first, then orders and customers, closing the day, money, staff access, settings and the AI assistant.'],
                    ['Test on small phones', 'Every screen was checked at the size of an entry-level Android phone, the kind most riders use.'],
                    ['Improve with the client', "The client's feedback was gathered in rounds and turned into improvements within hours."],
                ];
                foreach ($kl_steps as $kl_i => $kl_step): ?>
                <li class="relative p-6 border border-lavender/10 rounded-2xl hover:border-kl-orange/50 transition-colors duration-500 on-scroll">
                    <span class="font-syne text-5xl font-bold text-kl-orange/30"><?php echo str_pad((string) ($kl_i + 1), 2, '0', STR_PAD_LEFT); ?></span>
                    <h3 class="font-syne text-xl text-white mt-4 mb-3"><?php echo htmlspecialchars($kl_step[0]); ?></h3>
                    <p class="text-sm text-lavender/60 leading-relaxed"><?php echo htmlspecialchars($kl_step[1]); ?></p>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- ═══════════ TECH STACK ═══════════ -->
    <section class="py-20 border-t border-lavender/10 bg-[#050505]">
        <div class="max-w-4xl mx-auto px-4 text-center on-scroll">
            <h4 class="font-mono text-sm text-kl-orange mb-8 uppercase tracking-widest">// Built with</h4>
            <div class="flex flex-wrap justify-center gap-4 md:gap-6">
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">React Native (Expo)</span>
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">Supabase</span>
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">Offline-first sync</span>
                <span class="px-6 py-3 border border-white/10 rounded-full font-syne text-lg hover:bg-white hover:text-black transition-colors cursor-default">Google Gemini AI</span>
            </div>
        </div>
    </section>

    <!-- ═══════════ IN ONE LINE ═══════════ -->
    <section class="py-24 md:py-36 px-4 md:px-8 bg-kl-navy relative overflow-hidden">
        <div class="absolute -top-24 right-0 w-[28rem] h-[28rem] rounded-full bg-kl-orange/15 blur-3xl pointer-events-none"></div>
        <blockquote class="relative max-w-5xl mx-auto text-center on-scroll">
            <span class="font-mono text-xs text-kl-orange uppercase tracking-widest">In one line</span>
            <p class="font-syne text-3xl md:text-5xl text-white leading-tight mt-8">K-logistics Hub Ltd went from paper, phone calls and guesswork to <span class="text-kl-orange">one app where every order, delivery, product and naira is recorded and visible in real time.</span></p>
        </blockquote>
    </section>

    <!-- ═══════════ CTA ═══════════ -->
    <section class="py-24 md:py-32 px-4 md:px-6 text-center bg-matte-black border-t border-lavender/10 relative z-20">
        <div class="max-w-4xl mx-auto on-scroll">
            <h2 class="font-syne text-4xl md:text-7xl mb-6 text-white">Want an app like this <span class="text-kl-orange italic">for your business?</span></h2>
            <p class="text-xl md:text-2xl mb-12 text-lavender/80 leading-relaxed">If your orders, stock and money still live in notebooks and chats, we can design and build one app your whole team runs on, made for your real working conditions.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/contact" class="inline-block bg-kl-orange text-matte-black px-10 py-4 rounded-full text-lg font-bold uppercase tracking-widest hover:bg-white transition-all hover-target shadow-[0_0_24px_rgba(234,124,10,0.35)]">Start Your App</a>
                <a href="mailto:hello@getonlinestudio.com" class="inline-block px-10 py-4 border border-lavender/30 rounded-full text-sm uppercase tracking-widest hover:border-kl-orange hover:text-kl-orange transition-all hover-target">hello@getonlinestudio.com</a>
            </div>
        </div>
    </section>

    <!-- Next Project Nav -->
    <?php
    $go_prev = ['href' => '/work/dekompany', 'label' => 'DeKompany'];
    $go_next = ['href' => '/work/rafflekings', 'label' => 'RaffleKings'];
    include $_SERVER['DOCUMENT_ROOT'] . '/partials/project-nav.php';
    ?>

    <!-- Footer -->
    <?php $go_accent_class = 'kl-orange'; include $_SERVER['DOCUMENT_ROOT'] . '/partials/footer.php'; ?>

    <script>
        // Fade sections in as they scroll into view
        (function () {
            const items = document.querySelectorAll('.on-scroll');
            if (!('IntersectionObserver' in window)) {
                items.forEach(el => el.classList.add('is-visible'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            items.forEach(el => io.observe(el));
        })();

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
            document.querySelectorAll('.hover-target').forEach(el => {
                el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
            });
        }
    </script>
</body>
</html>
