<?php define("WP_USE_THEMES", false); require_once(__DIR__ . "/wp/wp-load.php"); ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- PRIMARY SEO -->
    <title>How to Get Your Business Online in <?= date('Y') ?> | GetOnline Studio</title>
    <meta name="description" content="A complete guide to getting your business online. Learn step-by-step how to build a website, rank on Google, and grow your brand online — whether you're in India, the UK, the US, or anywhere in the world.">
    <meta name="keywords" content="how to get online, get business online, get online, go online, how to start a website, get my business online, go online with my business, how to get your business online, get online studio">
    <meta name="author" content="GetOnline Studio">
    <link rel="canonical" href="https://getonlinestudio.com/get-online/">

    <!-- OPEN GRAPH -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://getonlinestudio.com/get-online/">
    <meta property="og:title" content="How to Get Your Business Online in <?= date('Y') ?> | GetOnline Studio">
    <meta property="og:description" content="A complete step-by-step guide to getting your business online. Domain, hosting, website, SEO — all of it explained in plain language.">
    <meta property="og:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="How to Get Your Business Online in <?= date('Y') ?> | GetOnline Studio">
    <meta name="twitter:description" content="A complete guide to getting your business online — from zero to a fully live, Google-ranked website.">
    <meta name="twitter:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- SCHEMA: ARTICLE -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "HowTo",
      "name": "How to Get Your Business Online",
      "description": "A complete step-by-step guide to getting any business online — from buying a domain to launching a Google-ranked website.",
      "totalTime": "P14D",
      "supply": [
        { "@type": "HowToSupply", "name": "Domain Name" },
        { "@type": "HowToSupply", "name": "Web Hosting" },
        { "@type": "HowToSupply", "name": "Business Email Address" }
      ],
      "step": [
        { "@type": "HowToStep", "name": "Choose Your Domain Name", "text": "Pick a domain name that is short, memorable, and matches your business name." },
        { "@type": "HowToStep", "name": "Get Web Hosting", "text": "Choose a fast, reliable web hosting provider to store your website files." },
        { "@type": "HowToStep", "name": "Build Your Website", "text": "Design a professional website that reflects your brand and converts visitors." },
        { "@type": "HowToStep", "name": "Set Up Business Email", "text": "Create a professional email address using your domain name." },
        { "@type": "HowToStep", "name": "Optimise for Search Engines", "text": "Implement on-page SEO so Google can find and rank your business." },
        { "@type": "HowToStep", "name": "Launch and Promote", "text": "Go live and start driving traffic from search, social, and paid channels." }
      ],
      "author": {
        "@type": "Organization",
        "name": "GetOnline Studio",
        "url": "https://getonlinestudio.com"
      }
    }
    </script>

    <!-- SCHEMA: FAQ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "How do I get my business online?", "acceptedAnswer": { "@type": "Answer", "text": "To get your business online, you need a domain name, web hosting, a professionally designed website, a business email, and basic SEO. GetOnline Studio can handle all of this for you end-to-end." } },
        { "@type": "Question", "name": "How much does it cost to get a business online?", "acceptedAnswer": { "@type": "Answer", "text": "The cost depends on your needs. A domain name typically costs $10 to $20 per year. Hosting ranges from $5 to $50 per month. A professionally designed website can range from $300 to $5,000 depending on complexity. GetOnline Studio offers transparent pricing for every stage." } },
        { "@type": "Question", "name": "How long does it take to get a business online?", "acceptedAnswer": { "@type": "Answer", "text": "With a professional agency like GetOnline Studio, a standard business website can be live within 7 to 14 days. More complex platforms like e-commerce sites or web apps can take 3 to 8 weeks." } },
        { "@type": "Question", "name": "Do I need a website if I already have social media?", "acceptedAnswer": { "@type": "Answer", "text": "Yes. Social media platforms own your audience. If they change their algorithm or shut down your account, you lose everything. Your website is digital real estate that you own and control forever." } },
        { "@type": "Question", "name": "Can GetOnline Studio help businesses outside Africa?", "acceptedAnswer": { "@type": "Answer", "text": "Absolutely. GetOnline Studio works with clients across India, the UK, the US, the UAE, Malaysia, Australia, Pakistan, and beyond. Our remote workflow is built for international projects." } }
      ]
    }
    </script>

    <!-- SCHEMA: BREADCRUMB -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://getonlinestudio.com/" },
        { "@type": "ListItem", "position": 2, "name": "Get Online", "item": "https://getonlinestudio.com/get-online/" }
      ]
    }
    </script>

    <!-- FAVICON -->
    <link rel="icon" type="image/jpeg" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&family=Syne:wght@400;700;800&family=Fira+Code:wght@400;600&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'matte-black': '#101010', 'card-dark': '#0a0a0a',
                        'lavender': '#e9d5ff', 'sharp-purple': '#7e22ce',
                        'off-white': '#f5f5f5', 'code-green': '#4ade80',
                        'panel-dark': '#151515',
                    },
                    fontFamily: {
                        'syne': ['Syne', 'sans-serif'],
                        'manrope': ['Manrope', 'sans-serif'],
                        'mono': ['Fira Code', 'monospace'],
                        'space': ['Space Grotesk', 'sans-serif'],
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #101010; color: #e9d5ff; overflow-x: hidden; cursor: none; }
        html { overflow-x: hidden; scroll-behavior: smooth; }
        
        /* Hide Scrollbar for internal widgets */
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }

        .cursor-dot, .cursor-outline { position: fixed; top: 0; left: 0; transform: translate(-50%,-50%); border-radius: 50%; z-index: 9999; pointer-events: none; }
        .cursor-dot { width: 8px; height: 8px; background-color: #e9d5ff; }
        .cursor-outline { width: 40px; height: 40px; border: 1px solid #7e22ce; transition: width 0.2s, height 0.2s, background-color 0.2s; }
        @media (pointer: coarse) { .cursor-dot, .cursor-outline { display: none; } body { cursor: auto !important; } }
        body.hovering .cursor-outline { width: 60px; height: 60px; background-color: rgba(126,34,206,0.2); border-color: transparent; }

        #mobile-menu { transition: transform 0.5s cubic-bezier(0.77,0,0.175,1); }
        #mobile-menu.open { transform: translateX(0); }

        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.5s cubic-bezier(0.77,0,0.175,1), opacity 0.4s ease; opacity: 0; }
        .faq-item.active .faq-answer { max-height: 600px; opacity: 1; }
        .faq-icon { transition: transform 0.3s ease; }
        .faq-item.active .faq-icon { transform: rotate(45deg); }

        .marquee-container { overflow: hidden; white-space: nowrap; display: flex; }
        .marquee-content { display: flex; flex-shrink: 0; min-width: 100%; animation: scroll 40s linear infinite; }
        @keyframes scroll { from { transform: translateX(0); } to { transform: translateX(-100%); } }

        .perspective-grid {
            position: absolute; width: 200%; height: 200%;
            background-image: linear-gradient(rgba(126,34,206,0.15) 1px,transparent 1px), linear-gradient(90deg,rgba(126,34,206,0.15) 1px,transparent 1px);
            background-size: 100px 100px;
            transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px);
            opacity: 0.2; pointer-events: none;
        }
        @media (min-width: 768px) {
            .perspective-grid { animation: gridMove 20s linear infinite; }
        }
        @keyframes gridMove { 0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); } 100% { transform: perspective(500px) rotateX(60deg) translateY(100px) translateZ(-200px); } }

        /* UPGRADED PROSE STYLING */
        .prose-guide p { 
            color: rgba(233,213,255,0.8); 
            font-family: 'Manrope', sans-serif;
            line-height: 1.8; 
            margin-bottom: 1.5rem; 
            font-size: 1.125rem; 
        }
        .prose-guide strong { color: #fff; font-weight: 700; }
        .prose-guide h2 { 
            font-family: 'Syne', sans-serif; 
            font-size: clamp(2rem, 4vw, 2.5rem); 
            font-weight: 800; 
            color: #fff; 
            margin-top: 3.5rem; 
            margin-bottom: 1.5rem; 
            line-height: 1.2; 
            letter-spacing: -0.02em;
        }
        .prose-guide h3 { 
            font-family: 'Syne', sans-serif; 
            font-size: clamp(1.4rem, 2.5vw, 1.75rem); 
            font-weight: 700; 
            color: #e9d5ff; 
            margin-top: 2.5rem; 
            margin-bottom: 1.25rem; 
            line-height: 1.3; 
        }
        @media (max-width: 768px) {
            .prose-guide p { font-size: 1.05rem; }
        }

        /* Step cards */
        .step-number { font-family: 'Fira Code', monospace; font-size: 0.75rem; color: #7e22ce; text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 0.5rem; font-weight: 600; }

        /* Stat block */
        .stat-item { padding-left: 1.5rem; border-left: 2px solid rgba(126, 34, 206, 0.4); }

        /* Location pill */
        .location-pill { display: inline-block; padding: 0.4rem 1rem; border: 1px solid rgba(233,213,255,0.15); border-radius: 9999px; font-family: 'Fira Code', monospace; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.15em; color: rgba(233,213,255,0.6); background: rgba(255,255,255,0.03); margin: 0.25rem; transition: all 0.3s ease; }
        .location-pill:hover { border-color: #7e22ce; color: #fff; background: rgba(126,34,206,0.1); }

        /* TOC */
        .toc-link { display: flex; align-items: center; gap: 0.75rem; font-family: 'Fira Code', monospace; font-size: 0.75rem; color: rgba(233,213,255,0.5); text-transform: uppercase; letter-spacing: 0.1em; padding: 0.75rem 0; border-bottom: 1px solid rgba(233,213,255,0.06); transition: all 0.3s; }
        .toc-link:hover { color: #fff; padding-left: 0.5rem; border-color: rgba(126, 34, 206, 0.5); }
        .toc-link .toc-num { color: #7e22ce; width: 1.5rem; font-weight: 600; }

        /* CTA banner */
        .cta-banner { background: linear-gradient(135deg, rgba(26,5,51,0.8) 0%, rgba(13,2,24,0.8) 60%, rgba(16,16,16,0.9) 100%); border: 1px solid rgba(126,34,206,0.3); position: relative; overflow: hidden; }
        .cta-banner::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(126,34,206,0.1) 0%, transparent 50%); pointer-events: none; }

        /* Reveal animation - ONLY USED ON HERO NOW for stability in reading */
        .reveal-up { opacity: 0; transform: translateY(40px); }
        .revealed { animation: fadeUp 0.8s cubic-bezier(0.25,1,0.5,1) forwards; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        /* Checklist */
        .check-item { display: flex; gap: 1rem; align-items: flex-start; padding: 1rem 0; border-bottom: 1px solid rgba(233,213,255,0.06); transition: background-color 0.3s; }
        .check-item:hover { background-color: rgba(255,255,255,0.02); }
        .check-icon { flex-shrink: 0; width: 1.5rem; height: 1.5rem; margin-top: 0.1rem; }

        /* WhatsApp Widget Animation */
        .widget-hidden { opacity: 0; transform: scale(0.95) translateY(10px); pointer-events: none; visibility: hidden; }
        .widget-visible { opacity: 1; transform: scale(1) translateY(0); pointer-events: auto; visibility: visible; }
    </style>
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <!-- Floating WhatsApp Widget -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">
        <!-- Widget Chat Window -->
        <div id="wa-float-window" class="widget-hidden mb-4 w-[90vw] max-w-[340px] bg-card-dark border border-lavender/20 rounded-2xl shadow-2xl flex-col overflow-hidden transition-all duration-300 origin-bottom-right pointer-events-auto">
            <div class="bg-[#151515] p-4 border-b border-lavender/10 flex justify-between items-center cursor-pointer hover-target" onclick="toggleWaWidget()">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="font-syne font-bold text-white">GetOnline Studio</span>
                </div>
                <button type="button" onclick="event.stopPropagation(); toggleWaWidget();" class="text-lavender/50 hover:text-white transition-colors focus:outline-none" style="pointer-events: auto;"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="p-5">
                <p class="text-sm font-manrope text-lavender/80 mb-4 leading-relaxed">
                    Hello! Ready to get your business online? Let us know what you need help with.
                </p>
                <div class="space-y-2 mb-5 max-h-[220px] overflow-y-auto hide-scrollbar pb-2">
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Website Design" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Website Design</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Web Development & Portals" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Web Development & Portals</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="SEO & Google Ranking" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">SEO & Google Ranking</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Branding & Logo Design" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Branding & Logo Design</span>
                    </label>
                    <label class="flex items-center gap-3 p-2.5 rounded-lg border border-white/5 bg-matte-black hover:border-sharp-purple/50 cursor-pointer transition-colors hover-target">
                        <input type="checkbox" value="Business Automation" class="wa-service-cb w-4 h-4 accent-sharp-purple rounded focus:ring-sharp-purple">
                        <span class="text-sm text-lavender/90 font-bold">Business Automation</span>
                    </label>
                </div>
                <button type="button" onclick="sendWaWidget()" class="w-full bg-[#25D366] text-white font-bold py-3.5 rounded-xl hover:bg-[#1ebe5d] transition-all hover-target shadow-lg shadow-[#25D366]/20 flex items-center justify-center gap-2 uppercase tracking-wide text-xs min-h-[44px]">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Continue to WhatsApp
                </button>
            </div>
        </div>
        <button type="button" id="wa-float-btn" onclick="toggleWaWidget()" class="w-14 h-14 bg-[#25D366] rounded-full flex items-center justify-center text-white shadow-[0_4px_20px_rgba(37,211,102,0.4)] hover:bg-[#1ebe5d] hover:scale-105 transition-all focus:outline-none hover-target pointer-events-auto">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobile-menu" class="fixed inset-0 bg-sharp-purple z-50 transform translate-x-full flex flex-col justify-center items-center text-center">
        <button id="close-menu" class="absolute top-6 right-6 text-matte-black font-syne font-bold text-xl p-4 hover-target">CLOSE</button>
        <nav class="flex flex-col gap-6">
            <a href="/work" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">WORK</a>
            <a href="/services" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">SERVICES</a>
            <a href="/about" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">ABOUT</a>
            <a href="/contact" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">CONTACT</a>
        </nav>
        <div class="absolute bottom-10 flex gap-6 font-mono text-xs text-matte-black/60 uppercase tracking-widest">
            <a href="https://wa.me/2349061150443" target="_blank" class="hover:text-white transition-colors">WhatsApp</a>
            <a href="mailto:hello@getonlinestudio.com" class="hover:text-white transition-colors">Email</a>
        </div>
    </div>

    <!-- NAV -->
    <nav class="fixed top-0 left-0 right-0 z-40 flex justify-between items-center px-4 md:px-8 py-4 md:py-5 bg-matte-black/80 backdrop-blur-md border-b border-lavender/10 pointer-events-none">
        <a href="/" style="pointer-events:auto;" class="font-syne font-bold text-xl md:text-2xl text-lavender hover:text-sharp-purple transition-colors hover-target">GO.</a>
        <button id="open-menu" style="pointer-events:auto;" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender/30 text-lavender px-5 md:px-6 py-2.5 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 hover-target">Menu</button>
    </nav>

    <!-- ═══════════════════════════════════════ -->
    <!-- HERO                                    -->
    <!-- ═══════════════════════════════════════ -->
    <header class="relative min-h-[85vh] flex flex-col justify-center items-center px-4 md:px-8 overflow-hidden pt-32 pb-20 border-b border-lavender/10">
        <div class="perspective-grid"></div>
        
        <!-- Floating Elements for Premium Feel -->
        <div class="absolute w-40 h-40 rounded-full border border-sharp-purple/20 top-[15%] left-[5%] animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute w-24 h-24 rotate-45 border-dashed border-lavender/20 bottom-[20%] right-[10%] animate-float" style="animation-delay: 2s;"></div>

        <!-- Breadcrumb -->
        <div class="absolute top-24 left-4 md:left-8 z-30 reveal-up" style="animation-delay: 0.1s;">
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 font-mono text-[10px] md:text-xs uppercase tracking-widest text-lavender/40">
                    <li><a href="/" class="hover:text-sharp-purple transition-colors hover-target">Home</a></li>
                    <li><span class="opacity-30">/</span></li>
                    <li><span class="text-lavender/70">Get Online</span></li>
                </ol>
            </nav>
        </div>

        <div class="relative z-20 w-full max-w-4xl mx-auto text-center mt-8 md:mt-0">
            <p class="font-mono text-[10px] md:text-xs font-bold text-code-green tracking-[0.2em] uppercase mb-6 inline-flex items-center gap-3 bg-code-green/10 px-4 py-2 rounded-full border border-code-green/20 backdrop-blur-sm reveal-up">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-code-green opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-code-green"></span></span>
                The Complete Guide
            </p>
            
            <h1 class="font-syne font-extrabold text-[9vw] md:text-6xl lg:text-7xl leading-[1.05] text-lavender mb-6 reveal-up" style="animation-delay: 0.1s;">
                How to Get Your<br>
                <span class="text-transparent text-stroke cursor-default hover:text-sharp-purple transition-colors duration-300">Business Online</span><br>
                (Step by Step)
            </h1>
            
            <p class="font-manrope text-lavender/70 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed mb-6 reveal-up" style="animation-delay: 0.2s;">
                Whether you are in <strong class="text-white">India, the UK, the US, the UAE, Australia,</strong> or anywhere else — this guide walks you through exactly what it takes to get your business online and in front of the right customers.
            </p>
            
            <p class="font-mono text-[10px] text-lavender/35 tracking-widest uppercase mb-10 reveal-up" style="animation-delay: 0.3s;">
                Last Updated: <?= date('F Y') ?> &nbsp;·&nbsp; 12 min read &nbsp;·&nbsp; By GetOnline Studio
            </p>

            <!-- Location pills -->
            <div class="mb-12 max-w-3xl mx-auto reveal-up" style="animation-delay: 0.4s;">
                <span class="location-pill">India</span>
                <span class="location-pill">United Kingdom</span>
                <span class="location-pill">United States</span>
                <span class="location-pill">UAE</span>
                <span class="location-pill">Malaysia</span>
                <span class="location-pill">Australia</span>
                <span class="location-pill">Pakistan</span>
                <span class="location-pill">Canada</span>
                <span class="location-pill">South Africa</span>
                <span class="location-pill">Global</span>
            </div>

            <div class="reveal-up" style="animation-delay: 0.5s;">
                <a href="#get-started" class="inline-flex items-center gap-3 bg-sharp-purple text-white px-10 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.4)]">
                    Start Reading <i data-lucide="arrow-down" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Stats bar -->
        <div class="relative z-20 mt-16 w-full max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 reveal-up" style="animation-delay: 0.7s;">
            <div class="text-center p-8 bg-card-dark border border-lavender/10 rounded-2xl hover:border-sharp-purple/40 transition-colors">
                <p class="font-syne text-4xl md:text-5xl font-bold text-sharp-purple mb-2">71%</p>
                <p class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest">of small businesses have a website</p>
            </div>
            <div class="text-center p-8 bg-card-dark border border-lavender/10 rounded-2xl hover:border-sharp-purple/40 transition-colors">
                <p class="font-syne text-4xl md:text-5xl font-bold text-sharp-purple mb-2">$5.8T</p>
                <p class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest">global e-commerce revenue</p>
            </div>
            <div class="text-center p-8 bg-card-dark border border-lavender/10 rounded-2xl hover:border-sharp-purple/40 transition-colors">
                <p class="font-syne text-4xl md:text-5xl font-bold text-sharp-purple mb-2">97%</p>
                <p class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest">of consumers search online before buying</p>
            </div>
        </div>
    </header>

    <!-- ═══════════════════════════════════════ -->
    <!-- MARQUEE                                 -->
    <!-- ═══════════════════════════════════════ -->
    <div class="border-b border-lavender/10 py-5 bg-sharp-purple overflow-hidden">
        <div class="marquee-container">
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                <?php $tags = ['GET ONLINE','DOMAIN NAME','WEB HOSTING','WEBSITE DESIGN','BUSINESS EMAIL','SEO','GOOGLE RANKING','DIGITAL PRESENCE','GET FOUND','GROW YOUR BRAND','GO LIVE','WEB DEVELOPMENT','ONLINE VISIBILITY','GET ONLINE STUDIO']; foreach(array_merge($tags,$tags) as $t): ?>
                <span class="px-6"><?= $t ?></span>✦
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════ -->
    <!-- MAIN CONTENT + SIDEBAR                  -->
    <!-- ═══════════════════════════════════════ -->
    <main id="get-started" class="max-w-7xl mx-auto px-4 md:px-8 py-20 md:py-32 relative z-10">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">

            <!-- SIDEBAR (TOC) -->
            <aside class="lg:col-span-4 mb-8 lg:mb-0">
                <div class="sticky top-32">
                    <div class="bg-card-dark border border-lavender/10 lg:border-none lg:bg-transparent rounded-3xl p-8 lg:p-0">
                        <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-6">[ Contents ]</p>
                        <nav class="space-y-1">
                            <a href="#why-get-online" class="toc-link hover-target"><span class="toc-num">01</span> Why Getting Online Matters</a>
                            <a href="#what-getting-online-means" class="toc-link hover-target"><span class="toc-num">02</span> What "Get Online" Means</a>
                            <a href="#step-domain" class="toc-link hover-target"><span class="toc-num">03</span> Step 1: Get Your Domain</a>
                            <a href="#step-hosting" class="toc-link hover-target"><span class="toc-num">04</span> Step 2: Choose Hosting</a>
                            <a href="#step-website" class="toc-link hover-target"><span class="toc-num">05</span> Step 3: Build Your Website</a>
                            <a href="#step-email" class="toc-link hover-target"><span class="toc-num">06</span> Step 4: Business Email</a>
                            <a href="#step-seo" class="toc-link hover-target"><span class="toc-num">07</span> Step 5: Show Up on Google</a>
                            <a href="#step-social" class="toc-link hover-target"><span class="toc-num">08</span> Step 6: Connect Socials</a>
                            <a href="#step-launch" class="toc-link hover-target"><span class="toc-num">09</span> Step 7: Launch and Grow</a>
                            <a href="#mistakes" class="toc-link hover-target"><span class="toc-num">10</span> Mistakes to Avoid</a>
                            <a href="#who-we-help" class="toc-link hover-target"><span class="toc-num">11</span> Who We Help</a>
                            <a href="#faq" class="toc-link hover-target"><span class="toc-num">12</span> FAQs</a>
                        </nav>
                    </div>

                    <!-- Sidebar CTA -->
                    <div class="mt-10 p-8 border border-sharp-purple/30 rounded-3xl bg-[#11081a] hidden lg:block">
                        <p class="font-syne text-xl font-bold text-white mb-3">Ready to scale?</p>
                        <p class="font-manrope text-sm text-lavender/70 mb-6 leading-relaxed">We handle everything — domain, hosting, design, SEO — so you don't have to guess.</p>
                        <a href="https://wa.me/2349061150443?text=Hi%20GetOnline%20Studio%2C%20I%20want%20to%20get%20my%20business%20online!" target="_blank" class="block text-center bg-sharp-purple text-white text-xs font-bold uppercase tracking-widest py-4 px-6 rounded-full hover:bg-white hover:text-matte-black transition-all hover-target shadow-lg">
                            Let's Talk
                        </a>
                    </div>
                </div>
            </aside>

            <!-- ARTICLE BODY - Removed scroll reveal classes for stable reading experience -->
            <article class="lg:col-span-8 prose-guide">

                <!-- ── SECTION 1 ── -->
                <section id="why-get-online" class="mb-20">
                    <div class="step-number">01 / Introduction</div>
                    <h2>Why Getting Online Is No Longer Optional</h2>

                    <p>Here is a hard truth.</p>
                    <p>If your business is not online right now, you are losing customers to someone who is. Every single day.</p>
                    <p>Think about the last time you needed a product or service. What did you do? You probably pulled out your phone and searched Google. Your customers are doing the exact same thing. And if you are not showing up, someone else is taking that sale.</p>

                    <p>The numbers back this up. According to <a href="https://backlinko.com/hub/seo" target="_blank" rel="noopener" class="text-sharp-purple underline underline-offset-4 hover:text-lavender transition-colors hover-target">Backlinko's SEO research</a>, <strong>68% of all online experiences begin with a search engine</strong>. That means more than two-thirds of your potential customers start their buying journey on Google, Bing, or similar platforms.</p>

                    <p>And then there is this: <strong>97% of consumers research a business online before they visit or buy</strong>. That statistic comes from multiple consumer behavior studies and has stayed consistent for years. It tells you one thing clearly: your online presence is your first impression. In most cases, it is the only impression you get.</p>

                    <!-- Stat block -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 my-12 p-8 md:p-10 bg-[#0f0f0f] border border-lavender/10 rounded-3xl">
                        <div class="stat-item">
                            <p class="font-syne text-4xl font-bold text-white mb-2">$5.8T</p>
                            <p class="font-manrope text-xs text-lavender/50 leading-relaxed">Global e-commerce revenue in 2023 (Statista)</p>
                        </div>
                        <div class="stat-item md:border-l border-lavender/10">
                            <p class="font-syne text-4xl font-bold text-white mb-2">71%</p>
                            <p class="font-manrope text-xs text-lavender/50 leading-relaxed">of small businesses now have a website (Zippia)</p>
                        </div>
                        <div class="stat-item md:border-l border-lavender/10">
                            <p class="font-syne text-4xl font-bold text-white mb-2">29%</p>
                            <p class="font-manrope text-xs text-lavender/50 leading-relaxed">are still invisible online — that is your competition gap</p>
                        </div>
                    </div>

                    <p>Whether you run a restaurant in Mumbai, a law firm in Manchester, a consultancy in Dubai, or a retail shop in Kuala Lumpur — the rules are the same. <strong>Your digital presence determines whether customers find you or your competitor.</strong></p>

                    <p>The good news? Getting online is not complicated. It is a clear, step-by-step process. And by the end of this guide, you will know exactly what to do.</p>
                </section>

                <!-- ── SECTION 2 ── -->
                <section id="what-getting-online-means" class="mb-20">
                    <div class="step-number">02 / Understanding the Basics</div>
                    <h2>What "Getting Online" Actually Means</h2>

                    <p>People hear "get online" and think: "Just make a Facebook page." Or "I already have Instagram, so I am fine."</p>

                    <p>That thinking will hurt you.</p>

                    <p>Social media platforms are rented land. You don't own your audience there. Facebook can change its algorithm tomorrow and cut your reach by 90%. Instagram can suspend your account for any reason. TikTok can be banned in your country. And when that happens, everything you built there disappears.</p>

                    <p>Getting online properly means building something <em>you own</em>. It means having a permanent address on the internet that belongs to you, ranks on Google, and works for your business 24 hours a day — even while you sleep.</p>

                    <p>Here is what a complete online presence looks like:</p>

                    <!-- Checklist -->
                    <div class="my-10 border border-lavender/10 rounded-3xl bg-[#0f0f0f] overflow-hidden">
                        <div class="p-6 border-b border-lavender/10 bg-[#151515]">
                            <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest">Your Online Presence Checklist</p>
                        </div>
                        <div class="p-6 md:p-8 divide-y divide-lavender/5">
                            <?php
                            $checklist = [
                                ['Your own domain name', 'yourcompany.com — an address that belongs to you forever.'],
                                ['Fast, reliable web hosting', 'The server that keeps your site live around the clock.'],
                                ['A professional website', 'Not a template. A site that converts visitors into customers.'],
                                ['A business email address', 'hello@yourcompany.com — not @gmail.com.'],
                                ['Google Business Profile', 'Shows your business on Google Maps and local search.'],
                                ['Basic on-page SEO', 'Helps Google understand what you do and who you serve.'],
                                ['Social media profiles', 'Linked to your website, not the other way around.'],
                                ['Google Analytics', 'So you can see who visits, from where, and what they do.'],
                            ];
                            foreach ($checklist as $item): ?>
                            <div class="check-item group">
                                <div class="w-6 h-6 rounded-full bg-code-green/10 flex items-center justify-center flex-shrink-0 mt-1 group-hover:bg-code-green transition-colors">
                                    <svg class="w-4 h-4 text-code-green group-hover:text-black transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-syne text-base md:text-lg font-bold text-white mb-1"><?= $item[0] ?></p>
                                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= $item[1] ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <p>All of this works together. Miss one piece and you have gaps in your digital foundation. Now let's walk through each one — step by step.</p>
                </section>

                <!-- ── SECTION 3 ── -->
                <section id="step-domain" class="mb-20">
                    <div class="step-number">03 / Step 1</div>
                    <h2>Get Your Domain Name</h2>

                    <p>Your domain name is your online address. It is what people type into a browser to find you. Think: <strong class="text-white">yourcompany.com</strong>.</p>

                    <p>Picking the right domain name matters more than most people realize. A good domain is short, easy to spell, and matches your business name. A bad domain is long, confusing, and impossible to remember.</p>

                    <h3>Rules for Choosing a Great Domain Name</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                        <?php
                        $domain_rules = [
                            ['Keep it short', 'Aim for 2 to 3 words maximum. Shorter domains are easier to type and remember.'],
                            ['Use .com if possible', '.com is still the most trusted extension globally. If taken, try .co or your country code (.in, .co.uk, .com.au).'],
                            ['No hyphens or numbers', 'These confuse people. "best-web-design-1.com" is a nightmare. Avoid it.'],
                            ['Match your brand name', 'Your domain and your business name should be the same. This builds trust and recognition.'],
                        ];
                        foreach ($domain_rules as $rule): ?>
                        <div class="p-8 border border-lavender/10 rounded-2xl bg-[#0f0f0f] hover:border-sharp-purple/40 transition-colors">
                            <p class="font-syne text-lg font-bold text-white mb-3"><?= $rule[0] ?></p>
                            <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= $rule[1] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h3>How Much Does a Domain Cost?</h3>

                    <p>A standard .com domain costs between <strong>$10 and $20 per year</strong>. That is less than a single dinner out. Popular registrars include Namecheap, GoDaddy, and Google Domains. Some hosting packages include a free domain for the first year.</p>

                    <p>Register your domain as soon as you have a business name. Domains are claimed on a first-come, first-served basis. If you wait, someone else could take yours.</p>

                    <!-- Tip box -->
                    <div class="p-8 border-l-4 border-sharp-purple bg-sharp-purple/5 rounded-r-2xl my-8">
                        <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-3 font-bold">Pro Tip</p>
                        <p class="font-manrope text-base text-lavender/80 leading-relaxed m-0">Buy your domain for at least 2 to 3 years upfront. Google uses domain age and registration length as a minor trust signal. It also protects you from forgetting to renew and losing your brand name.</p>
                    </div>

                    <p>If you are in <strong>India</strong>, search for your preferred domain on sites like BigRock or GoDaddy India. In the <strong>UK</strong>, 123-reg and Namecheap UK are popular. In <strong>Australia</strong>, VentraIP and Crazy Domains are widely used. Most registrars let you buy from anywhere in the world.</p>
                </section>

                <!-- ── SECTION 4 ── -->
                <section id="step-hosting" class="mb-20">
                    <div class="step-number">04 / Step 2</div>
                    <h2>Choose Your Web Hosting</h2>

                    <p>Web hosting is what keeps your website alive on the internet. Think of your domain as your address and hosting as the building at that address. Without hosting, your domain points to nothing.</p>

                    <p>Speed matters here. A lot. Research from <a href="https://developers.google.com/search/blog" target="_blank" rel="noopener" class="text-sharp-purple underline underline-offset-4 hover:text-lavender transition-colors hover-target">Google Search Central</a> confirms that page speed is a direct ranking factor. <strong>A one-second delay in load time can reduce conversions by up to 7%</strong>. A bad hosting provider can cost you customers every single day.</p>

                    <h3>Types of Hosting Explained Simply</h3>

                    <div class="space-y-6 my-8">
                        <?php
                        $hosting_types = [
                            ['Shared Hosting', 'Best for: New businesses, blogs, small websites', 'Cost: $3 to $15/month', 'Your website shares a server with hundreds of others. Affordable but slower during traffic spikes.', 'code-green'],
                            ['VPS Hosting', 'Best for: Growing businesses with medium traffic', 'Cost: $20 to $80/month', 'You get a dedicated slice of a server. Much faster and more stable than shared hosting.', 'lavender'],
                            ['Managed WP Hosting', 'Best for: WordPress sites that need to be fast', 'Cost: $15 to $60/month', 'Hosting optimized specifically for WordPress. Automatic updates, backups, and speed tuning included.', 'sharp-purple'],
                            ['Dedicated / Cloud', 'Best for: Large platforms, e-commerce, high traffic', 'Cost: $80 to $500+/month', 'An entire server just for you. Maximum speed, control, and scalability.', 'sharp-purple'],
                        ];
                        foreach ($hosting_types as $i => $h): ?>
                        <div class="p-6 md:p-8 border border-lavender/10 rounded-2xl bg-[#0f0f0f] flex flex-col md:flex-row gap-6 hover:border-sharp-purple/40 transition-colors">
                            <div class="font-mono text-<?= $h[4] ?> text-lg font-bold flex-shrink-0 pt-0.5">0<?= $i+1 ?></div>
                            <div>
                                <p class="font-syne text-xl font-bold text-white mb-2"><?= $h[0] ?></p>
                                <p class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest mb-4 inline-block bg-white/5 px-3 py-1 rounded-md"><?= $h[1] ?> &nbsp;·&nbsp; <?= $h[2] ?></p>
                                <p class="font-manrope text-base text-lavender/70 leading-relaxed"><?= $h[3] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p>For most new and small businesses, <strong>shared or managed WordPress hosting</strong> is the right starting point. Providers like SiteGround, Hostinger, and Cloudways are trusted globally and offer servers in multiple regions including Europe, Asia, and North America — which helps with load speed for your target audience.</p>

                    <p>If your audience is primarily in <strong>India</strong>, choose a host with servers in Asia. If you target the <strong>UK or Europe</strong>, pick a host with European data centers. Server location affects how quickly your site loads for your visitors.</p>
                </section>

                <!-- ── SECTION 5 ── -->
                <section id="step-website" class="mb-20">
                    <div class="step-number">05 / Step 3</div>
                    <h2>Build a Website That Actually Works</h2>

                    <p>This is where most businesses get it wrong.</p>

                    <p>They spend money on a domain and hosting, then build a website that looks like it was designed in 2009. Or worse, they use a free website builder and end up with something that looks exactly like every other business in their industry.</p>

                    <p>Your website is not just an online brochure. It is your best salesperson. It is working for you around the clock, in every time zone, every day of the year. It either converts visitors into customers — or it does not.</p>

                    <p>According to <a href="https://www.nngroup.com/articles/trustworthy-design/" target="_blank" rel="noopener" class="text-sharp-purple underline underline-offset-4 hover:text-lavender transition-colors hover-target">Nielsen Norman Group</a>, <strong>users form an opinion about your website in 0.05 seconds</strong>. That is 50 milliseconds. If your site looks unprofessional in that moment, they are gone — and they are not coming back.</p>

                    <h3>What Every Business Website Must Have</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                        <?php
                        $web_must = [
                            ['Clear headline', 'The first thing visitors see must tell them exactly what you do and who you serve. No guessing.'],
                            ['Fast load speed', 'Google recommends under 2.5 seconds for Core Web Vitals. Slow sites rank lower and lose visitors.'],
                            ['Mobile-first design', 'Over 60% of all web traffic is now on mobile. Your site must look perfect on every screen.'],
                            ['Clear call to action', 'Every page must tell visitors what to do next: Book a call. Send a message. Buy now. Make it obvious.'],
                            ['Trust signals', 'Client logos, testimonials, awards, certifications — proof that you are real and reliable.'],
                            ['Contact information', 'Phone, email, WhatsApp, address. Make it easy for customers to reach you immediately.'],
                            ['Service or product pages', 'Dedicated pages for each thing you offer. Not a vague "We do everything" paragraph.'],
                            ['SEO foundations', 'Title tags, meta descriptions, headers, image alt text — the basic signals Google needs to rank you.'],
                        ];
                        foreach ($web_must as $item): ?>
                        <div class="p-6 border border-lavender/10 rounded-2xl bg-[#0f0f0f] flex gap-4 hover:-translate-y-1 transition-transform duration-300">
                            <div class="w-6 h-6 rounded-full bg-code-green/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-code-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="font-syne text-base font-bold text-white mb-2"><?= $item[0] ?></p>
                                <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= $item[1] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h3>Should You Build It Yourself or Hire a Professional?</h3>

                    <p>This is the most common question we get. And the honest answer depends on your situation.</p>

                    <p>DIY website builders like Wix, Squarespace, and Webflow are fine for very simple use cases — a personal portfolio, a one-page landing page, or a basic blog. But there are real limitations:</p>

                    <div class="my-10 p-8 border border-lavender/10 rounded-3xl bg-[#0f0f0f]">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div>
                                <p class="font-mono text-xs text-lavender/50 uppercase tracking-widest mb-6 border-b border-lavender/10 pb-4">DIY Builder</p>
                                <ul class="space-y-4">
                                    <?php foreach (['Low upfront cost','Fast to set up','Limited SEO control','Generic design templates','Harder to scale','Ongoing monthly fees forever'] as $pro): ?>
                                    <li class="font-manrope text-sm text-lavender/60 flex items-start gap-3"><span class="text-lavender/30 mt-1">-</span><?= $pro ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div>
                                <p class="font-mono text-xs text-sharp-purple uppercase tracking-widest mb-6 border-b border-lavender/10 pb-4">Professional Build</p>
                                <ul class="space-y-4">
                                    <?php foreach (['Custom design that matches your brand','Full SEO control from day one','Faster performance architecture','Built to scale as you grow','You own the code completely','One-time investment, not forever fees'] as $pro): ?>
                                    <li class="font-manrope text-sm text-white flex items-start gap-3">
                                        <svg class="w-4 h-4 text-sharp-purple mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <?= $pro ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <p>For any serious business — whether you run a consultancy in <strong>Pakistan</strong>, a retail brand in <strong>Malaysia</strong>, or a services firm in <strong>the US</strong> — a professionally built website is not an expense. It is the most important investment you make in your brand. The right website pays for itself many times over in the customers it brings in.</p>

                    <!-- Mid-article CTA -->
                    <div class="cta-banner rounded-3xl p-8 md:p-12 my-12 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="relative z-10 text-center md:text-left">
                            <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-3">[ GetOnline Studio ]</p>
                            <p class="font-syne text-2xl md:text-3xl font-bold text-white mb-3">We build websites that rank and convert.</p>
                            <p class="font-manrope text-base text-lavender/70">From $350. Delivered in 7 to 14 days. For businesses globally.</p>
                        </div>
                        <a href="https://wa.me/2349061150443?text=Hi!%20I%20want%20to%20get%20my%20business%20online%20with%20GetOnline%20Studio." target="_blank" class="relative z-10 flex-shrink-0 bg-white text-matte-black px-8 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-sharp-purple hover:text-white transition-all hover-target shadow-xl">
                            Get a Free Quote
                        </a>
                    </div>
                </section>

                <!-- ── SECTION 6 ── -->
                <section id="step-email" class="mb-20">
                    <div class="step-number">06 / Step 4</div>
                    <h2>Set Up a Professional Business Email</h2>

                    <p>This one is simple. But a shocking number of businesses skip it.</p>

                    <p>Which email would you trust more?</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                        <div class="p-8 border border-red-500/20 rounded-2xl bg-red-500/5 text-center">
                            <p class="font-mono text-base md:text-lg text-red-400 font-bold mb-3">johnsbusiness2024@gmail.com</p>
                            <p class="font-manrope text-sm text-lavender/50">Looks unprofessional. Raises trust concerns immediately.</p>
                        </div>
                        <div class="p-8 border border-code-green/30 rounded-2xl bg-code-green/5 text-center shadow-[0_0_30px_rgba(74,222,128,0.05)]">
                            <p class="font-mono text-base md:text-lg text-code-green font-bold mb-3">john@yourcompany.com</p>
                            <p class="font-manrope text-sm text-lavender/70">Looks credible. Builds authority and trust instantly.</p>
                        </div>
                    </div>

                    <p>A business email address using your own domain costs about <strong>$6 per user per month</strong> with Google Workspace or Microsoft 365. That works out to less than $72 per year. For that price, you get a professional email, a calendar, cloud storage, and the trust that comes with it.</p>

                    <p>This is one of the easiest things to set up and one of the highest-impact changes you can make to how your business is perceived. Do not skip it.</p>
                </section>

                <!-- ── SECTION 7 ── -->
                <section id="step-seo" class="mb-20">
                    <div class="step-number">07 / Step 5</div>
                    <h2>Get Found on Google (SEO Basics)</h2>

                    <p>You could have the most beautiful website in the world. If no one can find it, it is useless.</p>

                    <p>Search Engine Optimization (SEO) is how you make sure Google understands your website and shows it to the right people at the right time. It is not magic. It is not a trick. It is a set of clear, learnable steps.</p>

                    <p>Here is how Google works: it sends small programs called "crawlers" across the internet to read websites. Based on what they find, Google decides which websites are the most relevant and trustworthy for different search terms. Your job is to make it easy for Google to understand you and trust you.</p>

                    <h3>The SEO Basics Every Business Needs</h3>

                    <div class="space-y-6 my-8">
                        <?php
                        $seo_steps = [
                            ['1', 'Keyword Research', 'Figure out exactly what your customers are searching for. Tools like Google Keyword Planner, Ahrefs, and Semrush help you find the right terms. Target keywords that match your services and have real search volume.'],
                            ['2', 'On-Page SEO', 'Every page on your site needs a unique title tag (under 60 characters), a meta description (under 160 characters), proper header structure (H1, H2, H3), and naturally written content that answers what searchers are looking for.'],
                            ['3', 'Google Business Profile', 'If you serve local customers — even in your city or country — claim your free Google Business Profile. This puts you on Google Maps and local search results. It is free and powerful. According to Google, businesses with complete profiles get 7x more clicks.'],
                            ['4', 'Backlinks', 'When other trusted websites link to yours, Google sees it as a vote of confidence. Getting listed in industry directories, getting press mentions, and publishing useful content all help build backlinks over time.'],
                            ['5', 'Page Speed & Mobile', 'Google uses Core Web Vitals as a ranking factor. This means your site must load fast and look perfect on mobile. According to data from <a href="https://www.smashingmagazine.com/categories/performance/" target="_blank" rel="noopener" class="text-sharp-purple hover:text-lavender transition-colors">Smashing Magazine</a>, pages that load in under 2 seconds have significantly lower bounce rates.'],
                        ];
                        foreach ($seo_steps as $step): ?>
                        <div class="p-6 md:p-8 border border-lavender/10 rounded-3xl bg-[#0f0f0f] flex flex-col md:flex-row gap-6 hover:border-sharp-purple/30 transition-all hover-target">
                            <div class="w-12 h-12 flex-shrink-0 rounded-2xl bg-sharp-purple/20 flex items-center justify-center">
                                <span class="font-syne text-sharp-purple text-xl font-bold"><?= $step[0] ?></span>
                            </div>
                            <div>
                                <p class="font-syne text-xl font-bold text-white mb-3"><?= $step[1] ?></p>
                                <p class="font-manrope text-base text-lavender/70 leading-relaxed m-0"><?= $step[2] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p>SEO is a long game. Most sites start to see meaningful results in 3 to 6 months. But the compounding effect is real. A page that ranks on page one of Google can bring in free, high-intent traffic for years — without paying for ads.</p>

                    <p>For businesses in <strong>the UK</strong>, local SEO with UK-specific keywords is critical. For businesses in <strong>India</strong>, ranking for city-specific terms like "web designer in Bangalore" or "digital agency in Mumbai" can drive massive local traffic. The principles are the same everywhere — the keywords just change.</p>
                </section>

                <!-- ── SECTION 8 ── -->
                <section id="step-social" class="mb-20">
                    <div class="step-number">08 / Step 6</div>
                    <h2>Connect Your Social Media (The Right Way)</h2>

                    <p>Social media is a tool. Not a strategy. And the biggest mistake businesses make is treating it as their entire online presence.</p>

                    <p>Here is the correct way to think about it: your website is home base. Social media is where you find people and bring them back to home base.</p>

                    <p>Every post, every bio, every ad should point people to your website. That is where they become leads, subscribers, or customers. Not on social media itself.</p>

                    <h3>Which Platforms Should You Use?</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 my-8">
                        <?php
                        // Hardcoded Inline SVGs so brand icons NEVER break or disappear when external libraries update
                        $platforms = [
                            ['LinkedIn', 'B2B, professional services, consulting, tech', '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sharp-purple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>'],
                            ['Instagram', 'Retail, fashion, food, lifestyle, beauty', '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sharp-purple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>'],
                            ['Facebook', 'Local businesses, community, older demographics', '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sharp-purple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>'],
                            ['X (Twitter)', 'News, tech, thought leadership, SaaS', '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sharp-purple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>'],
                            ['TikTok', 'Youth-focused brands, entertainment, viral content', '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sharp-purple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>'],
                            ['YouTube', 'Education, tutorials, long-form product demos', '<svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-sharp-purple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>'],
                        ];
                        foreach ($platforms as $p): ?>
                        <div class="p-6 border border-lavender/10 rounded-2xl bg-[#0f0f0f] flex flex-col items-start gap-4 hover:border-sharp-purple/40 transition-colors">
                            <?= $p[2] ?>
                            <div>
                                <p class="font-syne text-lg font-bold text-white mb-2"><?= $p[0] ?></p>
                                <p class="font-manrope text-xs text-lavender/60 leading-relaxed m-0"><?= $p[1] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p>Do not try to be on every platform. Pick 2 or 3 where your customers actually spend time. Do those well. A consistent presence on 2 platforms beats a scattered presence on 6.</p>

                    <p>And always — always — include your website URL in every profile bio. Every platform allows this. There is no reason not to use it.</p>
                </section>

                <!-- ── SECTION 9 ── -->
                <section id="step-launch" class="mb-20">
                    <div class="step-number">09 / Step 7</div>
                    <h2>Launch Your Website and Start Growing</h2>

                    <p>Before you hit publish, run through this pre-launch checklist.</p>

                    <div class="my-10 border border-lavender/10 rounded-3xl bg-[#0f0f0f] overflow-hidden">
                        <div class="p-6 md:p-8 border-b border-lavender/10 bg-[#151515]">
                            <p class="font-mono text-xs text-sharp-purple font-bold uppercase tracking-widest">Pre-Launch Checklist</p>
                        </div>
                        <div class="p-6 md:p-8 divide-y divide-lavender/5">
                            <?php
                            $launch = [
                                'Test your site comprehensively on mobile and desktop devices',
                                'Check all links — make sure none are broken or lead to 404s',
                                'Confirm contact forms work properly and emails arrive in your inbox',
                                'Set up Google Analytics 4 (GA4) to start tracking visitors immediately',
                                'Submit your XML sitemap to Google Search Console for indexing',
                                'Install and verify an SSL certificate (the padlock icon — HTTPS)',
                                'Set up 301 redirects for any old URLs if you are redesigning an existing site',
                                'Test your page load speed at Google PageSpeed Insights',
                                'Proofread every single page for typos, grammar, and factual errors',
                                'Add descriptive alt text to every image for SEO and accessibility compliance',
                            ];
                            foreach ($launch as $item): ?>
                            <div class="check-item group">
                                <div class="w-6 h-6 rounded-full bg-sharp-purple/10 flex items-center justify-center flex-shrink-0 mt-1 group-hover:bg-sharp-purple transition-colors">
                                    <svg class="w-3 h-3 text-sharp-purple group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p class="font-manrope text-base text-lavender/80 group-hover:text-white transition-colors"><?= $item ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <p>Once you are live, the real work begins. Getting online is not a one-time event. It is an ongoing process. The businesses that win online are the ones that consistently publish useful content, earn backlinks, improve their pages, and listen to what the data tells them.</p>

                    <p>But here is the thing: you do not have to do this alone. That is exactly what we are here for.</p>
                </section>

                <!-- ── SECTION 10 ── -->
                <section id="mistakes" class="mb-20">
                    <div class="step-number">10 / Common Pitfalls</div>
                    <h2>7 Mistakes That Kill Online Businesses Before They Start</h2>

                    <p>We have worked with hundreds of businesses and we see the same mistakes over and over. Here are the big ones — and how to avoid them.</p>

                    <div class="space-y-6 my-10">
                        <?php
                        $mistakes = [
                            ['Choosing the cheapest option for everything', 'Cheap hosting = slow website = bad rankings = lost customers. The same logic applies to design. Invest in quality where it matters.'],
                            ['Ignoring mobile users', 'Over 60% of global web traffic is on mobile. If your site is not optimized for phones, you are ignoring most of your audience.'],
                            ['No clear call to action', 'Visitors will not magically know what to do next. Tell them. Every page needs one clear next step.'],
                            ['Skipping Google Analytics', 'If you are not tracking your traffic, you are flying blind. You need to know where visitors come from, what they do, and where they leave.'],
                            ['Treating social media as your home base', 'Platforms come and go. Algorithms change. Your website is the only online asset you truly own.'],
                            ['Copying competitors instead of outperforming them', 'A template that looks like your competitor gets you nowhere. Be different. Be better.'],
                            ['Building once and forgetting', 'A website is not a set-and-forget asset. Regular content updates, speed checks, and SEO work are what keep you ranking.'],
                        ];
                        foreach ($mistakes as $i => $m): ?>
                        <div class="p-6 md:p-8 border border-red-500/10 rounded-2xl bg-[#0f0f0f] flex gap-6 hover:border-red-500/30 transition-colors">
                            <div class="w-10 h-10 flex-shrink-0 rounded-full bg-red-500/10 flex items-center justify-center font-mono text-red-400 font-bold">0<?= $i+1 ?></div>
                            <div>
                                <p class="font-syne text-lg font-bold text-white mb-2"><?= $m[0] ?></p>
                                <p class="font-manrope text-sm text-lavender/60 leading-relaxed m-0"><?= $m[1] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- ── SECTION 11 ── -->
                <section id="who-we-help" class="mb-20">
                    <div class="step-number">11 / About GetOnline Studio</div>
                    <h2>We Help Businesses Get Online and Scale</h2>

                    <p>You have just read the full playbook for getting your business online. Now comes the honest question: do you have the time, the skills, and the team to execute all of this yourself?</p>

                    <p>Most business owners do not. And that is not a weakness. It is just reality. You are busy running your actual business.</p>

                    <p>That is where <strong>GetOnline Studio</strong> comes in.</p>

                    <p>We are a digital agency built specifically to take businesses from zero online presence to a fully live, Google-optimized, professionally branded digital platform. We handle the domain, the hosting, the website design, the SEO, the business email — the whole thing. You run your business. We build your digital foundation.</p>

                    <h3>Who We Work With</h3>

                    <div class="flex flex-wrap gap-3 my-8">
                        <?php
                        $who = ['Startups','Law Firms','E-Commerce Brands','Consultancies','Restaurants','NGOs & Nonprofits','Healthcare Clinics','Real Estate Agencies','Schools & EdTech','Fintech Startups','SaaS Companies','Personal Brands'];
                        foreach ($who as $w): ?>
                        <div class="px-5 py-3 border border-lavender/10 rounded-full bg-card-dark text-center hover:bg-sharp-purple/10 hover:border-sharp-purple/30 transition-colors cursor-default">
                            <p class="font-manrope text-sm text-lavender/70 font-bold m-0"><?= $w ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h3>Where We Work</h3>

                    <p>We are a remote-first agency. Our clients are based all over the world. Whether you are in <strong>India, the United Kingdom, the United States, the UAE, Malaysia, Australia, Pakistan,</strong> or anywhere else — our workflow is built for international projects.</p>

                    <p>We communicate clearly, deliver on time, and build sites that perform in your market. No time zone is a barrier.</p>

                    <!-- Testimonial-style pull quote -->
                    <blockquote class="my-12 p-8 border-l-4 border-sharp-purple bg-[#0f0f0f] rounded-r-3xl">
                        <p class="font-syne text-xl md:text-2xl text-white leading-relaxed italic mb-6">"Our website is not just a site. It is a growth engine. GetOnline Studio understood our vision and built something that actually brings us clients."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-sharp-purple flex items-center justify-center text-white font-syne font-bold text-xs">GO</div>
                            <p class="font-mono text-[10px] text-lavender/50 uppercase tracking-widest m-0">GetOnline Studio Client</p>
                        </div>
                    </blockquote>

                    <p>If you are ready to stop being invisible and start showing up where your customers are looking, let us have a conversation.</p>

                    <!-- Final CTA -->
                    <div class="cta-banner rounded-3xl p-10 md:p-16 my-12 text-center shadow-2xl">
                        <p class="font-mono text-xs text-sharp-purple font-bold uppercase tracking-widest mb-4">[ Ready to Get Online? ]</p>
                        <h3 class="font-syne text-3xl md:text-5xl font-bold text-white mb-6 leading-tight">Your competitors are already online.<br>Let's get you there too.</h3>
                        <p class="font-manrope text-lavender/70 text-base md:text-lg mb-10 max-w-2xl mx-auto leading-relaxed">We handle domain, hosting, design, SEO, and business email — all under one roof. Get a free consultation, no pressure, no commitment.</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="https://wa.me/2349061150443?text=Hi%20GetOnline%20Studio%2C%20I%20want%20to%20get%20my%20business%20online!" target="_blank" class="w-full sm:w-auto bg-sharp-purple text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.4)]">
                                WhatsApp Us Now
                            </a>
                            <a href="mailto:hello@getonlinestudio.com" class="w-full sm:w-auto border border-lavender/30 text-lavender px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-lavender hover:text-matte-black transition-all hover-target">
                                Send an Email
                            </a>
                        </div>
                    </div>
                </section>

                <!-- ── SECTION 12: FAQ ── -->
                <section id="faq" class="mb-20">
                    <div class="step-number">12 / FAQs</div>
                    <h2>Frequently Asked Questions</h2>

                    <div class="space-y-4 mt-8">
                        <?php
                        $faqs = [
                            ['How do I get my business online?', 'You need five things: a domain name, web hosting, a professionally designed website, a business email, and basic SEO. GetOnline Studio handles all five — so you can focus on running your business.'],
                            ['How much does it cost to get a business online?', 'A domain name costs $10 to $20 per year. Hosting runs $5 to $50 per month depending on your plan. A professionally built website ranges from $350 to $5,000 depending on complexity. GetOnline Studio offers transparent pricing and a free quote — reach out and we will give you an exact figure for your needs.'],
                            ['How long does it take to get a business online?', 'With GetOnline Studio, a standard business website is live in 7 to 14 days. E-commerce platforms and custom web apps take 3 to 8 weeks. We give you a clear timeline before work starts.'],
                            ['Do I need a website if I already have a social media page?', 'Yes — absolutely. Social media platforms own your audience, not you. They can change their algorithm, suspend your account, or shut down entirely. Your website is digital real estate that you own and control. It also ranks on Google, which social media cannot replace.'],
                            ['What is the difference between a domain and hosting?', 'Your domain is your online address (yourcompany.com). Hosting is the server that stores your website files and keeps your site live. You need both. Many hosting providers let you buy both in one place.'],
                            ['Can GetOnline Studio help businesses outside Africa?', 'Yes. We work with clients across India, the UK, the US, the UAE, Malaysia, Australia, Pakistan, Canada, and beyond. Our remote workflow is structured for international projects. Language, time zone, and location are not barriers for us.'],
                            ['What is SEO and why does it matter?', 'SEO stands for Search Engine Optimization. It is the process of making your website visible on Google and other search engines. Without SEO, your website is like a shop in the middle of a desert — beautiful but impossible to find. Good SEO brings in free, high-intent traffic that converts into customers.'],
                            ['How do I get my website to show up on Google?', 'Start by submitting your sitemap to Google Search Console. Then focus on on-page SEO: relevant page titles, keyword-rich content, fast load speed, and mobile-friendly design. Build backlinks by getting listed in directories and earning mentions from other sites. It takes time, but the results are lasting.'],
                        ];
                        foreach ($faqs as $faq): ?>
                        <div class="faq-item border border-lavender/10 rounded-2xl bg-card-dark cursor-pointer hover-target overflow-hidden hover:border-sharp-purple/30 transition-colors">
                            <div class="flex justify-between items-center p-6 md:p-8 gap-4">
                                <p class="font-syne text-lg font-bold text-white"><?= $faq[0] ?></p>
                                <i data-lucide="plus" class="faq-icon w-5 h-5 text-sharp-purple flex-shrink-0"></i>
                            </div>
                            <div class="faq-answer px-6 md:px-8 pb-6 md:pb-8">
                                <p class="font-manrope text-base text-lavender/70 leading-relaxed m-0"><?= $faq[1] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            </article>
        </div>
    </main>

    <!-- ═══════════════════════════════════════ -->
    <!-- RELATED LINKS / INTERNAL SILO           -->
    <!-- ═══════════════════════════════════════ -->
    <section class="border-t border-lavender/10 py-20 md:py-32 px-4 md:px-8 bg-card-dark relative z-10">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-[10px] text-sharp-purple uppercase tracking-widest mb-10 text-center">[ Keep Reading ]</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $related = [
                    ['/services/', 'Our Services', 'Website design, SEO, branding, web apps — everything you need to get online and grow.', 'layers'],
                    ['/insights/', 'The Blog', 'Practical guides on web design, SEO, digital strategy, and running a smarter business online.', 'book-open'],
                    ['/contact/', 'Work With Us', 'Tell us about your project. We will get back to you within 24 hours with a clear plan.', 'briefcase'],
                ];
                foreach ($related as $r): ?>
                <a href="<?= $r[0] ?>" class="p-8 border border-lavender/10 rounded-3xl bg-matte-black hover:border-sharp-purple/50 transition-all duration-300 hover:-translate-y-2 hover-target group flex flex-col gap-5">
                    <div class="w-12 h-12 rounded-xl bg-sharp-purple/10 flex items-center justify-center group-hover:bg-sharp-purple transition-colors duration-300">
                        <i data-lucide="<?= $r[3] ?>" class="w-5 h-5 text-sharp-purple group-hover:text-white transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-syne text-xl font-bold text-white mb-3 group-hover:text-sharp-purple transition-colors flex items-center gap-2"><?= $r[1] ?> <i data-lucide="arrow-right" class="w-4 h-4"></i></p>
                        <p class="font-manrope text-sm text-lavender/60 leading-relaxed"><?= $r[2] ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FOOTER                                  -->
    <!-- ═══════════════════════════════════════ -->
    <footer class="bg-[#0d0d0d] border-t border-lavender/10 relative z-20 overflow-hidden px-4 md:px-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(126,34,206,0.08),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.05),transparent_28%)] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative">
            
            <div class="py-16 md:py-20 grid lg:grid-cols-2 gap-12 border-b border-lavender/10">
                <div>
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Let's Build Something Strong ]</p>
                    <h2 class="font-syne text-4xl md:text-6xl font-bold text-white leading-tight mb-5">GetOnline Studio</h2>
                    <p class="font-manrope text-lavender/65 text-sm md:text-base leading-relaxed max-w-xl mb-6">
                        We build serious websites and digital systems for businesses across Nigeria and globally. From design and SEO to branding, automation, and support, we help your organisation look credible, get found, and grow with confidence.
                    </p>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm">
                        <a href="mailto:hello@getonlinestudio.com" class="inline-flex items-center gap-2 text-sharp-purple hover:text-white transition-colors hover-target break-all">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            <span>hello@getonlinestudio.com</span>
                        </a>
                        <a href="https://wa.me/2349061150443" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-[#25D366] hover:text-white transition-colors hover-target">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>+234 906 115 0443</span>
                        </a>
                    </div>
                </div>

                <div class="lg:pt-4">
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ This Guide ]</p>
                    <h3 class="font-syne text-2xl font-bold text-white mb-4">The Blueprint for Digital Growth</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed mb-4">
                        This page is part of our comprehensive educational ecosystem designed to help business owners understand the mechanics of the internet.
                    </p>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">
                        Whether you need a new website, stronger visibility on Google, or a smarter digital foundation, we can help you move with clarity. Let's make the internet work for you.
                    </p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="py-12 grid grid-cols-2 md:grid-cols-4 gap-8 border-b border-lavender/10">
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Company</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/" class="text-lavender/60 hover:text-white transition-colors hover-target">Home</a></li>
                        <li><a href="/about/" class="text-lavender/60 hover:text-white transition-colors hover-target">About Us</a></li>
                        <li><a href="/testimonials/" class="text-lavender/60 hover:text-white transition-colors hover-target">Testimonials</a></li>
                        <li><a href="/work/" class="text-lavender/60 hover:text-white transition-colors hover-target">Projects &amp; Case Studies</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Services</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/services/web-design/" class="text-lavender/60 hover:text-white transition-colors hover-target">Website Design</a></li>
                        <li><a href="/services/seo/" class="text-lavender/60 hover:text-white transition-colors hover-target">SEO &amp; Google Ranking</a></li>
                        <li><a href="/services/branding/" class="text-lavender/60 hover:text-white transition-colors hover-target">Branding &amp; Identity</a></li>
                        <li><a href="/services/" class="text-lavender/60 hover:text-sharp-purple transition-colors hover-target font-bold">All Services &rarr;</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Explore</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/locations/" class="text-lavender/60 hover:text-white transition-colors hover-target">Service Locations</a></li>
                        <li><a href="/insights/" class="text-lavender/60 hover:text-white transition-colors hover-target">Digital Insights Blog</a></li>
                        <li><a href="/get-online/" class="text-lavender/60 hover:text-white transition-colors hover-target">How to Get Online</a></li>
                        <li><a href="/contact/" class="text-lavender/60 hover:text-white transition-colors hover-target">Start a Project</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Legal</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/privacy-policy/" class="text-lavender/60 hover:text-white transition-colors hover-target">Privacy Policy</a></li>
                        <li><a href="/terms-of-service/" class="text-lavender/60 hover:text-white transition-colors hover-target">Terms of Service</a></li>
                        <li><a href="/cookie-policy/" class="text-lavender/60 hover:text-white transition-colors hover-target">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-8 pb-10 flex flex-col md:flex-row justify-between items-center gap-4 font-manrope text-xs text-lavender/30">
                <p>GetOnline Studio &copy; <?= date('Y') ?>. All rights reserved.</p>
                <p class="font-mono uppercase tracking-widest">Digital Infrastructure. Built for Growth.</p>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent -->
    <div id="cookie-banner" class="fixed bottom-4 md:bottom-6 right-4 md:right-6 max-w-xs w-[calc(100%-2rem)] z-50 transform translate-y-20 opacity-0 transition-all duration-700 pointer-events-none">
        <div class="bg-card-dark/95 backdrop-blur-md border border-lavender/10 p-5 rounded-2xl shadow-2xl">
            <h4 class="font-syne text-sharp-purple text-xs font-bold uppercase tracking-widest mb-2">( COOKIES )</h4>
            <p class="font-manrope text-xs text-lavender/70 mb-4 leading-relaxed">We use cookies to ensure you get the best experience. <a href="/privacy-policy" class="text-white underline decoration-sharp-purple underline-offset-4 hover:text-sharp-purple transition-colors">Read Policy</a>.</p>
            <div class="flex gap-3">
                <button id="accept-cookies" class="flex-1 bg-lavender text-matte-black font-syne font-bold text-[10px] uppercase tracking-widest py-3 rounded-xl hover:bg-white transition-colors cursor-pointer hover-target">Accept</button>
                <button id="decline-cookies" class="flex-1 border border-lavender/20 text-lavender font-syne font-bold text-[10px] uppercase tracking-widest py-3 rounded-xl hover:bg-lavender/10 transition-colors cursor-pointer hover-target">Decline</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // Mobile menu
        const menu = document.getElementById('mobile-menu');
        document.getElementById('open-menu').addEventListener('click', () => {
            menu.classList.add('open'); document.body.style.overflow = 'hidden';
        });
        document.getElementById('close-menu').addEventListener('click', () => {
            menu.classList.remove('open'); document.body.style.overflow = '';
        });

        // WhatsApp Widget Toggle logic
        function toggleWaWidget() {
            const waWindow = document.getElementById('wa-float-window');
            if (!waWindow) return;
            if (waWindow.classList.contains('widget-hidden')) {
                waWindow.classList.remove('widget-hidden');
                waWindow.classList.add('widget-visible');
            } else {
                waWindow.classList.remove('widget-visible');
                waWindow.classList.add('widget-hidden');
            }
        }
        function sendWaWidget() {
            const checkedBoxes = document.querySelectorAll('.wa-service-cb:checked');
            let selectedServices = [];
            checkedBoxes.forEach(cb => selectedServices.push(cb.value));
            let waText = selectedServices.length > 0
                ? `Hi GetOnline Studio, I was reading your Get Online Guide. I am interested in getting help with: ${selectedServices.join(', ')}. Can we talk?`
                : `Hi GetOnline Studio, I was reading your Get Online Guide and I'm ready to get my business online. Let's talk!`;
            window.open(`https://wa.me/2349061150443?text=${encodeURIComponent(waText)}`, '_blank');
            toggleWaWidget();
        }

        // Custom cursor logic (desktop only)
        if (!isTouchDevice) {
            const cursorDot = document.querySelector('.cursor-dot');
            const cursorOutline = document.querySelector('.cursor-outline');
            let mouseX = 0, mouseY = 0, outlineX = 0, outlineY = 0;
            
            window.addEventListener('mousemove', e => {
                mouseX = e.clientX; mouseY = e.clientY;
                cursorDot.style.transform = `translate(${mouseX}px,${mouseY}px) translate(-50%,-50%)`;
            });
            
            const animateCursor = () => {
                outlineX += (mouseX - outlineX) * 0.15;
                outlineY += (mouseY - outlineY) * 0.15;
                cursorOutline.style.transform = `translate(${outlineX}px,${outlineY}px) translate(-50%,-50%)`;
                requestAnimationFrame(animateCursor);
            };
            animateCursor();
            
            document.querySelectorAll('.hover-target, a, button, .faq-item, .check-item').forEach(el => {
                el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
            });
        }

        // FAQ accordion logic
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                if (!isActive) item.classList.add('active');
            });
        });

        // Scroll reveal logic (ONLY APPLIED TO HERO HEADER NOW)
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    if(!entry.target.style.animationDelay) {
                        entry.target.style.animationDelay = (i * 0.08) + 's';
                    }
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
        
        document.querySelectorAll('.reveal-up').forEach(el => revealObserver.observe(el));

        // Cookie banner logic
        const cookieBanner = document.getElementById('cookie-banner');
        if (!localStorage.getItem('cookieConsent')) {
            setTimeout(() => cookieBanner.classList.remove('translate-y-20','opacity-0','pointer-events-none'), 2500);
        }
        document.getElementById('accept-cookies').addEventListener('click', () => {
            localStorage.setItem('cookieConsent','accepted');
            cookieBanner.classList.add('translate-y-20','opacity-0','pointer-events-none');
        });
        document.getElementById('decline-cookies').addEventListener('click', () => {
            localStorage.setItem('cookieConsent','declined');
            cookieBanner.classList.add('translate-y-20','opacity-0','pointer-events-none');
        });

        // Smooth scroll adjustment for fixed sticky headers
        document.querySelectorAll('.toc-link, a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const targetId = a.getAttribute('href');
                if(targetId === '#') return;
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    const y = target.getBoundingClientRect().top + window.scrollY - 120;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>