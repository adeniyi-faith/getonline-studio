<?php define("WP_USE_THEMES", false); require_once(__DIR__ . "/wp/wp-load.php"); ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- ═══ PRIMARY SEO ═══ -->
    <title>GetOnline Studio | Web Design Agency in Nigeria   Web Designer, Developer & Digital Company</title>
    <meta name="description" content="GetOnline Studio is Nigeria's leading web design agency. We are a full-service web design company offering professional website design, web development, branding, SEO, and digital infrastructure for businesses across Nigeria and globally.">
    <meta name="keywords" content="web design agency Nigeria, web designer Nigeria, web design company Nigeria, web developer Nigeria, web design firm Nigeria, website design services Nigeria, web design Lagos, web design Abuja, digital agency Nigeria, GetOnline Studio">
    <meta name="author" content="GetOnline Studio">
    <link rel="canonical" href="https://getonlinestudio.com/">

    <!-- ═══ FAVICON ═══ -->
    <link rel="icon" type="image/jpeg" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <link rel="apple-touch-icon" href="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- ═══ OPEN GRAPH ═══ -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://getonlinestudio.com/">
    <meta property="og:title" content="GetOnline Studio | Web Design Agency in Nigeria">
    <meta property="og:description" content="Nigeria's leading web design company. We build high-converting websites, web apps, and brand identities for businesses across Nigeria and the world.">
    <meta property="og:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="GetOnline Studio | Web Design Agency in Nigeria">
    <meta name="twitter:description" content="Nigeria's leading web design company. Professional websites, apps, branding & SEO.">
    <meta name="twitter:image" content="https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg">

    <!-- ═══ SCHEMA: ORGANIZATION ═══ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfessionalService",
      "name": "GetOnline Studio",
      "url": "https://getonlinestudio.com",
      "logo": "https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg",
      "image": "https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg",
      "description": "GetOnline Studio is a web design agency and digital infrastructure company in Nigeria, helping businesses get online, look credible, and grow.",
      "telephone": "+2349061150443",
      "email": "hello@getonlinestudio.com",
      "address": { "@type": "PostalAddress", "addressCountry": "NG" },
      "areaServed": "Nigeria",
      "priceRange": "₦₦₦",
      "sameAs": [
        "https://twitter.com/getonlinestudio",
        "https://instagram.com/getonlinestudio",
        "https://linkedin.com/company/getonlinestudio"
      ]
    }
    </script>

    <!-- ═══ SCHEMA: FAQ ═══ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        { "@type": "Question", "name": "How much does a website cost in Nigeria?", "acceptedAnswer": { "@type": "Answer", "text": "Website costs in Nigeria typically range from ₦120,000 for a basic business site to ₦600,000+ for complex platforms, custom web apps, or enterprise systems. GetOnline Studio provides a free discovery call to give you an accurate quote based on your specific needs." } },
        { "@type": "Question", "name": "What is the difference between a web designer and a web developer in Nigeria?", "acceptedAnswer": { "@type": "Answer", "text": "A web designer focuses on how your site looks   layout, colours, user experience, and visual identity. A web developer focuses on how it works   the code, database, performance, and backend logic. At GetOnline Studio, we are both: a full-service web design firm with in-house designers and developers." } },
        { "@type": "Question", "name": "Do you work with businesses outside Nigeria?", "acceptedAnswer": { "@type": "Answer", "text": "Yes. We are a digital-first agency. We have successfully delivered projects for clients in India, the UK, the US, and across Africa. Our remote workflow is structured to handle international projects with the same efficiency as local ones." } },
        { "@type": "Question", "name": "How long does it take to build a professional website?", "acceptedAnswer": { "@type": "Answer", "text": "A standard business website typically takes 7 to 14 days. More complex systems   like web portals, e-commerce platforms, or mobile apps   take 3 to 8 weeks. We give you a clear timeline before work starts." } },
        { "@type": "Question", "name": "Is GetOnline Studio a registered company in Nigeria?", "acceptedAnswer": { "@type": "Answer", "text": "Yes. GetOnline Studio is a registered digital agency in Nigeria. We also help other businesses with CAC registration as part of our service offerings." } }
      ]
    }
    </script>

    <!-- ═══ FONTS ═══ -->
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
                        'syne': ['Syne', 'sans-serif'], 'manrope': ['Manrope', 'sans-serif'],
                        'mono': ['Fira Code', 'monospace'], 'space': ['Space Grotesk', 'sans-serif'],
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    },
                    animation: {
                        'spin-slow': 'spin 15s linear infinite',
                        'spin-slow-reverse': 'spin 20s linear infinite reverse',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4,0,0.6,1) infinite',
                    },
                    keyframes: {
                        float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-20px)' } }
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="/assets/css/index.css">
<?php wp_head(); ?>
</head>
<body class="bg-matte-black bg-noise font-manrope selection:bg-sharp-purple selection:text-white relative">

    <div class="cursor-dot"></div>
    <div class="cursor-outline"></div>

    <!-- INTRO OVERLAY -->
    <div id="intro-overlay" class="fixed inset-0 bg-matte-black z-[100] flex items-center justify-center px-4 text-center">
        <h1 class="font-syne text-5xl md:text-9xl font-bold text-lavender overflow-hidden">
            <span class="reveal-text block">GET ONLINE.</span>
        </h1>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobile-menu" class="fixed inset-0 bg-sharp-purple z-50 transform translate-x-full flex flex-col justify-center items-center text-center">
        <button id="close-menu" class="absolute top-6 right-6 text-matte-black font-syne font-bold text-xl p-4 hover-target">CLOSE</button>
        <nav class="flex flex-col gap-6">
            <a href="/work" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">WORK</a>
            <a href="/services" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">SERVICES</a>
            <a href="/about" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">ABOUT</a>
            <a href="/locations/" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">LOCATIONS</a>
            <a href="/contact" class="font-syne text-5xl md:text-7xl text-matte-black font-bold hover:text-white transition-colors hover-target">CONTACT</a>
        </nav>
        <div class="absolute bottom-10 flex gap-6 font-mono text-xs text-matte-black/60 uppercase tracking-widest">
            <a href="https://wa.me/2349061150443" target="_blank" class="hover:text-matte-black transition-colors">WhatsApp</a>
            <a href="mailto:hello@getonlinestudio.com" class="hover:text-matte-black transition-colors">Email</a>
        </div>
    </div>

    <!-- NAV -->
    <nav class="fixed top-0 w-full z-40 px-4 md:px-8 py-4 md:py-6 flex justify-between items-center mix-blend-difference text-lavender" style="pointer-events:none;">
        <a href="/" style="pointer-events:auto;" class="font-syne font-bold text-xl md:text-2xl hover:text-sharp-purple transition-colors hover-target">GO.</a>
        <button id="open-menu" style="pointer-events:auto;" class="text-xs md:text-sm font-bold tracking-widest uppercase border border-lavender px-4 md:px-6 py-2 rounded-full hover:bg-lavender hover:text-matte-black transition-all duration-300 bg-matte-black/20 backdrop-blur-sm hover-target">
            Menu
        </button>
    </nav>

    <!-- ═══════════════════════════════════════ -->
    <!-- HERO                                    -->
    <!-- ═══════════════════════════════════════ -->
    <header class="relative min-h-[90vh] md:min-h-screen flex flex-col justify-center items-center px-4 md:px-6 overflow-hidden pt-20">
        <div class="perspective-grid"></div>
        <div class="kinetic-wrapper w-full text-center relative z-10 flex-1 flex flex-col justify-center" style="opacity:0; animation: reveal 1s ease 2.8s forwards;">
            <!-- FIXED: Boosted contrast and size for better readability -->
            <p class="font-mono text-[10px] md:text-xs font-bold text-code-green tracking-[0.2em] md:tracking-[0.3em] uppercase mb-6 opacity-0" style="animation: fadeUp 0.8s ease 3s forwards;">
                [ Web Design Agency · Nigeria · Global ]
            </p>
            <h1 class="font-syne font-extrabold text-[11vw] md:text-[12vw] leading-[0.88] uppercase text-lavender flex flex-col justify-center items-center w-full overflow-hidden">
                <div class="kinetic-text md:whitespace-nowrap px-2 w-full" data-speed="0.04">Digital</div>
                <div class="kinetic-text text-sharp-purple md:whitespace-nowrap px-2 w-full" data-speed="-0.04">Studio</div>
            </h1>
            <p class="font-manrope text-lavender/60 text-base md:text-xl max-w-2xl mx-auto mt-8 leading-relaxed opacity-0" style="animation: fadeUp 0.9s ease 3.2s forwards;">
                We are a <strong class="text-white">web design company</strong> and <strong class="text-white">digital infrastructure agency</strong> helping businesses across Nigeria   and the world   look credible, get found, and grow.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0" style="animation: fadeUp 0.9s ease 3.4s forwards;">
                <a href="#services" class="w-full sm:w-auto bg-sharp-purple text-white px-8 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-[0_0_30px_rgba(126,34,206,0.4)]">
                    See Our Services
                </a>
                <a href="https://wa.me/2349061150443?text=Hi%20GetOnline%20Studio%2C%20I%20want%20to%20discuss%20a%20project!" target="_blank" class="w-full sm:w-auto border border-lavender/40 text-lavender px-8 py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-lavender hover:text-matte-black transition-all hover-target flex items-center justify-center gap-2">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp Us
                </a>
            </div>
        </div>

        <!-- FIXED: Removed absolute positioning, added flex-wrap, improved contrast -->
        <div class="relative mt-12 md:mt-16 w-full flex flex-wrap justify-center items-center gap-6 sm:gap-10 opacity-0 pb-10 z-20" style="animation: fadeUp 1s ease 3.6s forwards;">
            <div class="text-center min-w-[110px]">
                <p class="font-syne text-2xl md:text-3xl font-bold text-white">50+</p>
                <p class="font-mono text-[9px] text-lavender/70 font-bold uppercase tracking-widest mt-1">Projects Delivered</p>
            </div>
            <div class="hidden sm:block w-px h-8 bg-lavender/20"></div>
            <div class="text-center min-w-[110px]">
                <p class="font-syne text-2xl md:text-3xl font-bold text-white">7+</p>
                <p class="font-mono text-[9px] text-lavender/70 font-bold uppercase tracking-widest mt-1">Years Experience</p>
            </div>
            <div class="hidden sm:block w-px h-8 bg-lavender/20"></div>
            <div class="text-center min-w-[110px]">
                <p class="font-syne text-2xl md:text-3xl font-bold text-white">10+</p>
                <p class="font-mono text-[9px] text-lavender/70 font-bold uppercase tracking-widest mt-1">Cities Served</p>
            </div>
        </div>
    </header>

    <!-- ═══════════════════════════════════════ -->
    <!-- WHO WE ARE                              -->
    <!-- ═══════════════════════════════════════ -->
    <section class="relative py-20 md:py-32 px-4 md:px-8 bg-matte-black border-t border-lavender/10 z-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-16 items-start">
            <div class="md:col-span-4">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-6">[ Who We Are ]</p>
                <h2 class="font-syne text-3xl md:text-4xl font-bold text-white leading-tight mb-6">
                    Not just a web design firm.<br>A digital infrastructure partner.
                </h2>
                <a href="/about" class="inline-flex items-center gap-2 border-b border-sharp-purple pb-1 text-lavender hover:text-sharp-purple transition-colors font-syne uppercase tracking-widest text-xs hover-target">
                    Our Story <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
            <div class="md:col-span-8 prose-dark">
                <p class="font-syne text-2xl md:text-4xl leading-tight mb-8 text-lavender">
                    We build the platform. <span class="text-sharp-purple italic">You build the business.</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">
                    <div>
                        <p>Most Nigerian businesses run on a WhatsApp number and an Instagram page. That is a starting point   not a strategy. The moment a potential client Googles you and finds nothing, you lose that deal to whoever shows up first.</p>
                        <p>We are a <strong>web design agency</strong> that also happens to understand business. We don't build websites for aesthetics alone. Every platform we create is engineered to make you look credible, rank on Google, and convert visitors into paying customers.</p>
                    </div>
                    <div>
                        <p>Our team combines <strong>web designers</strong>, <strong>web developers</strong>, brand strategists, and SEO specialists under one roof. Whether you need a clean business website, a complex web portal, a mobile app, or a complete brand overhaul, we handle it all in-house.</p>
                        <p>We have delivered projects for law firms, fintech startups, NGOs, schools, churches, hospitals, and e-commerce brands   from Osogbo to India. When you are ready to invest in your digital future, we are ready to build it.</p>
                    </div>
                </div>
                <!-- Keyword coverage chips -->
                <div class="flex flex-wrap gap-2 mt-8">
                    <span class="px-3 py-1.5 bg-white/5 border border-lavender/10 rounded-full text-[10px] font-mono uppercase tracking-widest text-lavender/50">Web Design Agency</span>
                    <span class="px-3 py-1.5 bg-white/5 border border-lavender/10 rounded-full text-[10px] font-mono uppercase tracking-widest text-lavender/50">Web Design Company</span>
                    <span class="px-3 py-1.5 bg-white/5 border border-lavender/10 rounded-full text-[10px] font-mono uppercase tracking-widest text-lavender/50">Web Designer</span>
                    <span class="px-3 py-1.5 bg-white/5 border border-lavender/10 rounded-full text-[10px] font-mono uppercase tracking-widest text-lavender/50">Web Developer</span>
                    <span class="px-3 py-1.5 bg-white/5 border border-lavender/10 rounded-full text-[10px] font-mono uppercase tracking-widest text-lavender/50">Web Design Firm</span>
                    <span class="px-3 py-1.5 bg-white/5 border border-lavender/10 rounded-full text-[10px] font-mono uppercase tracking-widest text-lavender/50">Nigeria</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- SERVICES (Stacked Sticky)               -->
    <!-- ═══════════════════════════════════════ -->
    <section id="services" class="relative w-full">

        <div class="py-16 md:py-20 px-4 md:px-8 text-center border-t border-lavender/10 bg-[#0a0a0a] z-10 relative">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ What We Do ]</p>
            <h2 class="font-syne text-3xl md:text-5xl max-w-3xl mx-auto text-white">Everything you need to <span class="text-sharp-purple italic">dominate online.</span></h2>
            <p class="font-manrope text-lavender/60 text-lg max-w-2xl mx-auto mt-4">From a simple business website to a full-scale digital infrastructure   we are the only web design firm you will ever need.</p>
        </div>

        <!-- 01   Website Design -->
        <div class="service-card min-h-[60vh] md:h-screen w-full bg-[#050505] border-t border-lavender/10 overflow-hidden flex flex-col md:flex-row hover-target">
            <div class="w-full md:w-1/2 p-6 md:p-16 flex flex-col justify-center z-10 py-16 md:py-0">
                <div class="font-mono text-sharp-purple text-sm mb-4">01</div>
                <h3 class="font-syne text-4xl md:text-7xl mb-5 leading-none text-white">WEBSITE<br>DESIGN</h3>
                <p class="font-manrope text-lavender/70 text-base md:text-lg mb-6 max-w-md leading-relaxed">
                    This is where most clients start. Your website is your most important sales tool. A poorly designed site costs you clients every single day. We build websites that load fast, look exceptional on every device, and turn visitors into leads.
                </p>
                <p class="font-manrope text-lavender/50 text-sm mb-8 max-w-md leading-relaxed">
                    As a professional <strong class="text-lavender/80">web design company in Nigeria</strong>, we handle everything: structure, copywriting direction, visuals, contact forms, Google integration, and SEO-ready architecture. You don't lift a finger.
                </p>
                <div class="flex flex-wrap gap-2 mb-8">
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Business Websites</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Landing Pages</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">E-Commerce</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">UI/UX Design</span>
                </div>
                <a href="/services/web-design/" class="group inline-flex items-center gap-2 text-sharp-purple hover:text-white font-bold uppercase tracking-widest text-sm transition-colors hover-target">
                    Learn More <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>
            <div class="w-full md:w-1/2 min-h-[30vh] md:h-full bg-matte-black relative overflow-hidden border-t md:border-t-0 md:border-l border-lavender/10 flex justify-center items-center py-10 md:py-0">
                <div class="absolute inset-0 bg-[linear-gradient(rgba(126,34,206,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(126,34,206,0.08)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] h-[70%] border border-lavender/15 rounded-xl bg-black/60 backdrop-blur-md overflow-hidden shadow-[0_0_60px_rgba(126,34,206,0.2)]">
                    <div class="h-8 border-b border-lavender/10 flex items-center gap-2 px-4">
                        <div class="w-2 h-2 rounded-full bg-red-500/70"></div>
                        <div class="w-2 h-2 rounded-full bg-yellow-500/70"></div>
                        <div class="w-2 h-2 rounded-full bg-green-500/70"></div>
                        <div class="ml-4 flex-1 h-4 bg-white/5 rounded-sm"></div>
                    </div>
                    <div class="p-6 space-y-4 opacity-60">
                        <div class="w-2/3 h-3 bg-sharp-purple/40 rounded animate-pulse"></div>
                        <div class="w-full h-28 bg-lavender/5 rounded border border-lavender/10"></div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="h-14 bg-lavender/5 rounded border border-lavender/10"></div>
                            <div class="h-14 bg-lavender/5 rounded border border-lavender/10"></div>
                            <div class="h-14 bg-lavender/5 rounded border border-lavender/10"></div>
                        </div>
                        <div class="w-1/3 h-8 bg-sharp-purple/30 rounded-full"></div>
                    </div>
                    <div class="absolute top-0 left-0 w-full h-0.5 bg-sharp-purple shadow-[0_0_20px_#7e22ce] scan-line"></div>
                </div>
            </div>
        </div>

        <!-- 02   Web Development -->
        <div class="service-card min-h-[60vh] md:h-screen w-full bg-card-dark border-t border-lavender/10 overflow-hidden flex flex-col md:flex-row hover-target">
            <div class="w-full md:w-1/2 p-6 md:p-16 flex flex-col justify-center z-10 py-16 md:py-0">
                <div class="font-mono text-sharp-purple text-sm mb-4">02</div>
                <h3 class="font-syne text-4xl md:text-7xl mb-5 leading-none text-white">WEB<br>DEVELOPMENT</h3>
                <p class="font-manrope text-lavender/70 text-base md:text-lg mb-6 max-w-md leading-relaxed">
                    Need something more powerful than a standard website? Our <strong class="text-lavender">web developers</strong> build custom portals, booking systems, membership platforms, fintech dashboards, and web applications that run your business logic automatically.
                </p>
                <p class="font-manrope text-lavender/50 text-sm mb-8 max-w-md leading-relaxed">
                    We are not just web designers   we are engineers. If you can describe what you need your platform to do, we can build it. Custom backends, payment integration (Paystack, Flutterwave), API connections, and database architecture are all part of our stack.
                </p>
                <div class="flex flex-wrap gap-2 mb-8">
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Web Portals</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Custom Backends</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Booking Systems</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Payment Integration</span>
                </div>
                <a href="/services/development/" class="group inline-flex items-center gap-2 text-sharp-purple hover:text-white font-bold uppercase tracking-widest text-sm transition-colors hover-target">
                    Learn More <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>
            <div class="w-full md:w-1/2 min-h-[30vh] md:h-full bg-matte-black relative overflow-hidden border-t md:border-t-0 md:border-l border-lavender/10 flex justify-center items-center py-10 md:py-0">
                <div class="relative w-full h-full flex justify-center items-center opacity-70">
                    <div class="absolute w-[280px] h-[280px] border border-sharp-purple/20 rounded-full animate-spin-slow-reverse"></div>
                    <div class="absolute w-[180px] h-[180px] border border-lavender/10 rounded-full animate-spin-slow"></div>
                    <div class="flex flex-col gap-3 z-10 w-52">
                        <div class="w-full h-11 bg-sharp-purple/15 backdrop-blur-md border border-sharp-purple/40 rounded flex items-center px-4 overflow-hidden relative">
                            <div class="w-full h-full absolute top-0 left-0 code-rain opacity-40"></div>
                            <span class="font-mono text-xs text-lavender relative z-10">init_portal_auth();</span>
                        </div>
                        <div class="w-full h-11 bg-lavender/5 backdrop-blur-md border border-lavender/20 rounded flex items-center px-4 overflow-hidden relative md:[transform:translateX(28px)]">
                            <div class="w-full h-full absolute top-0 left-0 code-rain opacity-40" style="animation-delay:0.6s"></div>
                            <span class="font-mono text-xs text-lavender relative z-10">connect_paystack();</span>
                        </div>
                        <div class="w-full h-11 bg-code-green/5 backdrop-blur-md border border-code-green/20 rounded flex items-center px-4 relative md:[transform:translateX(14px)]">
                            <span class="font-mono text-xs text-code-green relative z-10">✓ deploy_success</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 03   SEO & Google Ranking -->
        <div class="service-card min-h-[60vh] md:h-screen w-full bg-[#0c0c0c] border-t border-lavender/10 overflow-hidden flex flex-col md:flex-row hover-target">
            <div class="w-full md:w-1/2 p-6 md:p-16 flex flex-col justify-center z-10 py-16 md:py-0">
                <div class="font-mono text-sharp-purple text-sm mb-4">03</div>
                <h3 class="font-syne text-4xl md:text-7xl mb-5 leading-none text-white">SEO &<br>GOOGLE</h3>
                <p class="font-manrope text-lavender/70 text-base md:text-lg mb-6 max-w-md leading-relaxed">
                    A beautiful website that nobody finds is just an expensive brochure. We optimise your platform so that when people in your city   or your country   search for what you offer, your business appears at the top.
                </p>
                <p class="font-manrope text-lavender/50 text-sm mb-8 max-w-md leading-relaxed">
                    Every site we build comes with core on-page SEO baked in from day one. We also offer dedicated <strong class="text-lavender/80">Local SEO</strong> and <strong class="text-lavender/80">Google Business Profile</strong> optimisation to dominate your city's search results and the Maps pack.
                </p>
                <div class="flex flex-wrap gap-2 mb-8">
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">On-Page SEO</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Local SEO</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Google Maps</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Keyword Strategy</span>
                </div>
                <a href="/services/seo/" class="group inline-flex items-center gap-2 text-sharp-purple hover:text-white font-bold uppercase tracking-widest text-sm transition-colors hover-target">
                    Learn More <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>
            <div class="w-full md:w-1/2 min-h-[30vh] md:h-full bg-matte-black relative overflow-hidden border-t md:border-t-0 md:border-l border-lavender/10 flex justify-center items-center py-10 md:py-0">
                <div class="w-full max-w-xs mx-auto px-4">
                    <div class="bg-white/5 border border-lavender/10 rounded-xl p-5 mb-4">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-3 h-3 rounded-full bg-code-green animate-pulse"></div>
                            <span class="font-mono text-[10px] text-code-green uppercase tracking-widest">Page 1 Ranking</span>
                        </div>
                        <p class="font-syne text-sm text-white mb-1">Your Business Name</p>
                        <p class="font-mono text-[10px] text-lavender/40">getonlinestudio.com</p>
                        <p class="font-manrope text-xs text-lavender/50 mt-2">★★★★★ 4.9 · Web Design Agency Nigeria</p>
                    </div>
                    <div class="bg-white/3 border border-lavender/5 rounded-xl p-5 opacity-40">
                        <p class="font-syne text-sm text-lavender/60 mb-1">Competitor A</p>
                        <p class="font-mono text-[10px] text-lavender/30">competitor.com</p>
                    </div>
                    <div class="bg-white/3 border border-lavender/5 rounded-xl p-5 opacity-20 mt-3">
                        <p class="font-syne text-sm text-lavender/40 mb-1">Competitor B</p>
                        <p class="font-mono text-[10px] text-lavender/20">another-site.com</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 04   Brand Identity -->
        <div class="service-card min-h-[60vh] md:h-screen w-full bg-[#0f0f0f] border-t border-lavender/10 overflow-hidden flex flex-col md:flex-row hover-target">
            <div class="w-full md:w-1/2 p-6 md:p-16 flex flex-col justify-center z-10 py-16 md:py-0">
                <div class="font-mono text-sharp-purple text-sm mb-4">04</div>
                <h3 class="font-syne text-4xl md:text-7xl mb-5 leading-none text-white">BRAND<br>IDENTITY</h3>
                <p class="font-manrope text-lavender/70 text-base md:text-lg mb-6 max-w-md leading-relaxed">
                    People judge credibility in seconds. Before they read a single word on your website, they have already formed an opinion based on how your brand looks. We build brand identities that command respect and make people take you seriously.
                </p>
                <p class="font-manrope text-lavender/50 text-sm mb-8 max-w-md leading-relaxed">
                    Logo design, colour systems, typography, brand guidelines, social media kits   we create the complete visual language that makes your business memorable across every touchpoint, online and offline.
                </p>
                <div class="flex flex-wrap gap-2 mb-8">
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Logo Design</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Brand Guidelines</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Visual Identity</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Social Media Kit</span>
                </div>
                <a href="/services/branding/" class="group inline-flex items-center gap-2 text-sharp-purple hover:text-white font-bold uppercase tracking-widest text-sm transition-colors hover-target">
                    Learn More <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>
            <div class="w-full md:w-1/2 min-h-[30vh] md:h-full bg-matte-black relative overflow-hidden border-t md:border-t-0 md:border-l border-lavender/10 flex items-center justify-center py-10 md:py-0">
                <div class="relative w-48 h-48 md:w-64 md:h-64">
                    <div class="absolute inset-0 border-2 border-sharp-purple rounded-full animate-spin-slow opacity-50" style="animation-duration:20s;"></div>
                    <div class="absolute inset-4 border border-lavender rounded-full animate-spin-slow opacity-30" style="animation-duration:15s; animation-direction:reverse;"></div>
                    <div class="absolute inset-10 border-4 border-dotted border-lavender/15 rounded-full animate-spin-slow" style="animation-duration:30s;"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 font-syne text-7xl md:text-9xl text-white mix-blend-overlay animate-pulse-slow">Aa</div>
                </div>
            </div>
        </div>

        <!-- 05   Mobile Apps & Custom Dev -->
        <div class="service-card min-h-[60vh] md:h-screen w-full bg-[#141414] border-t border-lavender/10 overflow-hidden flex flex-col md:flex-row hover-target">
            <div class="w-full md:w-1/2 p-6 md:p-16 flex flex-col justify-center z-10 py-16 md:py-0">
                <div class="font-mono text-sharp-purple text-sm mb-4">05</div>
                <h3 class="font-syne text-4xl md:text-7xl mb-5 leading-none text-white">MOBILE<br>APPS</h3>
                <p class="font-manrope text-lavender/70 text-base md:text-lg mb-6 max-w-md leading-relaxed">
                    Some businesses need more than a website. If you need your customers to download and use an app, or if your internal operations require a mobile solution, we build powerful Android and iOS applications engineered for performance and scale.
                </p>
                <p class="font-manrope text-lavender/50 text-sm mb-8 max-w-md leading-relaxed">
                    From fintech and gaming platforms to church apps and booking systems, we have built complex mobile ecosystems with custom backends. Your app will look world-class and perform flawlessly under load.
                </p>
                <div class="flex flex-wrap gap-2 mb-8">
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Android Apps</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">iOS Apps</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Custom Backend</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">API Architecture</span>
                </div>
                <a href="/services/development/" class="group inline-flex items-center gap-2 text-sharp-purple hover:text-white font-bold uppercase tracking-widest text-sm transition-colors hover-target">
                    Learn More <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>
            <div class="w-full md:w-1/2 min-h-[30vh] md:h-full bg-matte-black relative overflow-hidden border-t md:border-t-0 md:border-l border-lavender/10 flex items-center justify-center py-10 md:py-0">
                <div class="relative w-36 h-64 md:w-44 md:h-80 border-2 border-lavender/20 rounded-3xl bg-[#080808] overflow-hidden shadow-[0_0_60px_rgba(126,34,206,0.15)]">
                    <div class="h-6 border-b border-lavender/10 flex items-center justify-center">
                        <div class="w-12 h-1 bg-lavender/20 rounded-full"></div>
                    </div>
                    <div class="p-3 space-y-2 opacity-60">
                        <div class="w-full h-20 bg-sharp-purple/10 rounded-xl border border-sharp-purple/20"></div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="h-12 bg-lavender/5 rounded-lg border border-lavender/10"></div>
                            <div class="h-12 bg-lavender/5 rounded-lg border border-lavender/10"></div>
                        </div>
                        <div class="w-full h-8 bg-sharp-purple/20 rounded-lg"></div>
                    </div>
                    <div class="absolute top-0 left-0 w-full h-0.5 bg-sharp-purple shadow-[0_0_12px_#7e22ce] scan-line"></div>
                </div>
            </div>
        </div>

        <!-- 06   Business Tools & Automation -->
        <div class="service-card min-h-[60vh] md:h-screen w-full bg-[#111] border-t border-lavender/10 overflow-hidden flex flex-col md:flex-row hover-target">
            <div class="w-full md:w-1/2 p-6 md:p-16 flex flex-col justify-center z-10 py-16 md:py-0">
                <div class="font-mono text-sharp-purple text-sm mb-4">06</div>
                <h3 class="font-syne text-4xl md:text-7xl mb-5 leading-none text-white">BUSINESS<br>TOOLS</h3>
                <p class="font-manrope text-lavender/70 text-base md:text-lg mb-6 max-w-md leading-relaxed">
                    Your business should run even when you are asleep. We set up smart digital systems   automated customer replies, CRM integrations, lead capture workflows, and AI-powered tools   that handle the repetitive work so you can focus on what matters.
                </p>
                <p class="font-manrope text-lavender/50 text-sm mb-8 max-w-md leading-relaxed">
                    We also help businesses with <strong class="text-lavender/80">CAC Registration</strong>, <strong class="text-lavender/80">Social Media Setup</strong>, and <strong class="text-lavender/80">Website Maintenance</strong> plans. Think of us as your complete digital department.
                </p>
                <div class="flex flex-wrap gap-2 mb-8">
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Business Automation</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">CRM Setup</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">CAC Registration</span>
                    <span class="px-3 py-1.5 border border-lavender/20 rounded-full text-[10px] uppercase tracking-widest text-lavender/60">Maintenance</span>
                </div>
                <a href="/services/" class="group inline-flex items-center gap-2 text-sharp-purple hover:text-white font-bold uppercase tracking-widest text-sm transition-colors hover-target">
                    All Services <span class="group-hover:translate-x-2 transition-transform">→</span>
                </a>
            </div>
            <div class="w-full md:w-1/2 min-h-[30vh] md:h-full bg-matte-black relative overflow-hidden border-t md:border-t-0 md:border-l border-lavender/10 flex items-center justify-center py-10 md:py-0">
                <div class="relative w-full max-w-xs mx-auto px-4 h-48 overflow-hidden">
                    <div class="absolute top-0 left-6 bg-lavender/5 p-4 rounded-xl border border-lavender/15 animate-float w-36 sm:w-44">
                        <div class="h-1.5 w-1/2 bg-sharp-purple rounded mb-2"></div>
                        <div class="h-1.5 w-3/4 bg-lavender/20 rounded"></div>
                    </div>
                    <div class="absolute top-8 right-6 bg-sharp-purple/10 p-4 rounded-xl border border-sharp-purple/30 animate-float w-28 sm:w-36 z-10" style="animation-delay:1s;">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-sharp-purple flex items-center justify-center text-[8px] font-bold text-white">AI</div>
                            <div class="h-1.5 w-full bg-white/20 rounded"></div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-10 bg-lavender/5 p-4 rounded-xl border border-lavender/10 animate-float w-40 sm:w-48" style="animation-delay:2s;">
                        <div class="flex justify-between mb-2">
                            <div class="h-1.5 w-8 bg-lavender/40 rounded"></div>
                            <div class="h-1.5 w-8 bg-code-green/50 rounded"></div>
                        </div>
                        <div class="h-10 w-full bg-black/40 rounded border border-white/5"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CAPABILITY MARQUEE -->
    <section class="py-5 bg-sharp-purple overflow-hidden">
        <div class="marquee-container">
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                <span class="mx-6">STRATEGY</span>✦<span class="mx-6">WEB DESIGN</span>✦<span class="mx-6">DEVELOPMENT</span>✦<span class="mx-6">SEO</span>✦<span class="mx-6">BRANDING</span>✦<span class="mx-6">MOBILE APPS</span>✦<span class="mx-6">AUTOMATION</span>✦<span class="mx-6">NIGERIA</span>✦
            </div>
            <div class="marquee-content font-space font-bold text-black text-lg uppercase tracking-widest">
                <span class="mx-6">STRATEGY</span>✦<span class="mx-6">WEB DESIGN</span>✦<span class="mx-6">DEVELOPMENT</span>✦<span class="mx-6">SEO</span>✦<span class="mx-6">BRANDING</span>✦<span class="mx-6">MOBILE APPS</span>✦<span class="mx-6">AUTOMATION</span>✦<span class="mx-6">NIGERIA</span>✦
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- SELECTED WORK                           -->
    <!-- ═══════════════════════════════════════ -->
    <section class="relative pt-20 md:pt-32 pb-10 bg-matte-black z-20 border-t border-lavender/10">
        <div class="max-w-7xl mx-auto px-4 md:px-8 mb-16">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Selected Work ]</p>
            <h2 class="font-syne text-3xl md:text-6xl font-bold text-white max-w-4xl leading-tight">Platforms we've built for ambitious brands.</h2>
        </div>
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-14 md:gap-20">

                <a href="/work/rafflekings" class="group block hover-target">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <img src="https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69c056ef61d91271e5147a38.jpg" alt="RaffleKings fintech platform by GetOnline Studio" class="w-full h-full object-cover object-top filter grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute top-4 left-4"><span class="font-mono text-[10px] text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">01</span></div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                        <div>
                            <h3 class="font-syne text-3xl font-bold mb-2 group-hover:text-sharp-purple transition-colors">RAFFLEKINGS</h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md">A high-performance Fintech-Gaming ecosystem with a custom PHP backend, mobile app, and real-time payment integration.</p>
                        </div>
                        <span class="font-mono text-[10px] text-sharp-purple border border-sharp-purple/30 px-3 py-1 rounded-full whitespace-nowrap self-start">Fintech / App</span>
                    </div>
                </a>

                <a href="/work/visionafric" class="group block hover-target md:mt-20">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <img src="https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfd90c35c39c6a8b6658d9.jpg" alt="VisionAfric NGO website by GetOnline Studio" class="w-full h-full object-cover object-top filter grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute top-4 left-4"><span class="font-mono text-[10px] text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">02</span></div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                        <div>
                            <h3 class="font-syne text-3xl font-bold mb-2 group-hover:text-sharp-purple transition-colors">VISIONAFRIC</h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md">Trust Architecture designed to legitimize a pan-African NGO mission and drive high-ticket international donations.</p>
                        </div>
                        <span class="font-mono text-[10px] text-sharp-purple border border-sharp-purple/30 px-3 py-1 rounded-full whitespace-nowrap self-start">NGO / Global</span>
                    </div>
                </a>

                <a href="/work/wip" class="group block hover-target">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <img src="https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfd3409c44e64ed26ba1d9.jpg" alt="World Institute for Peace website" class="w-full h-full object-cover object-top filter grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute top-4 left-4"><span class="font-mono text-[10px] text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">03</span></div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                        <div>
                            <h3 class="font-syne text-3xl font-bold mb-2 group-hover:text-sharp-purple transition-colors">W.I.P</h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md">Authoritative digital architecture for a global peace institution combining classic typography and dynamic API integrations.</p>
                        </div>
                        <span class="font-mono text-[10px] text-sharp-purple border border-sharp-purple/30 px-3 py-1 rounded-full whitespace-nowrap self-start">International</span>
                    </div>
                </a>

                <a href="/work/oa-global" class="group block hover-target md:mt-20">
                    <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-6 bg-card-dark border border-lavender/10">
                        <img src="https://getonlinestudio.com/insights/wp-content/uploads/2026/03/69bfd9cf5715882b8b47aab1.jpg" alt="OA Global microfinance platform" class="w-full h-full object-cover object-top filter grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute top-4 left-4"><span class="font-mono text-[10px] text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">04</span></div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                        <div>
                            <h3 class="font-syne text-3xl font-bold mb-2 group-hover:text-sharp-purple transition-colors">OA GLOBAL</h3>
                            <p class="font-manrope text-lavender/60 text-sm leading-relaxed max-w-md">Enterprise-grade microfinance ecosystem automating complex lending workflows with institutional authority and security.</p>
                        </div>
                        <span class="font-mono text-[10px] text-sharp-purple border border-sharp-purple/30 px-3 py-1 rounded-full whitespace-nowrap self-start">Enterprise App</span>
                    </div>
                </a>

            </div>
        </div>
        <div class="py-16 md:py-24 text-center">
            <a href="/work" class="font-syne text-xl md:text-3xl hover:text-sharp-purple transition-colors underline decoration-1 underline-offset-8 hover-target">VIEW FULL PORTFOLIO →</a>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- KEYWORD COVERAGE SECTION               -->
    <!-- ═══════════════════════════════════════ -->
    <section class="py-16 md:py-20 px-4 md:px-8 bg-[#0a0a0a] border-t border-lavender/10 relative z-20">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-8 text-center">[ How Clients Find Us ]</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-card-dark border border-lavender/10 rounded-2xl p-6 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="pen-tool" class="w-5 h-5 text-sharp-purple mb-4"></i>
                    <h3 class="font-syne text-base font-bold text-white mb-2">Web Designer in Nigeria</h3>
                    <p class="text-xs text-lavender/50 leading-relaxed font-manrope">Looking for a skilled web designer in Nigeria? We craft custom, brand-aligned websites that convert visitors into customers.</p>
                </div>
                <div class="bg-card-dark border border-lavender/10 rounded-2xl p-6 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="code-2" class="w-5 h-5 text-sharp-purple mb-4"></i>
                    <h3 class="font-syne text-base font-bold text-white mb-2">Web Developer in Nigeria</h3>
                    <p class="text-xs text-lavender/50 leading-relaxed font-manrope">Need a professional web developer? We build fast, functional, and scalable platforms from clean, robust code.</p>
                </div>
                <div class="bg-card-dark border border-lavender/10 rounded-2xl p-6 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="building-2" class="w-5 h-5 text-sharp-purple mb-4"></i>
                    <h3 class="font-syne text-base font-bold text-white mb-2">Web Design Agency Nigeria</h3>
                    <p class="text-xs text-lavender/50 leading-relaxed font-manrope">A full-service web design agency with in-house designers, developers, and strategists working on your project together.</p>
                </div>
                <div class="bg-card-dark border border-lavender/10 rounded-2xl p-6 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="briefcase" class="w-5 h-5 text-sharp-purple mb-4"></i>
                    <h3 class="font-syne text-base font-bold text-white mb-2">Web Design Company Nigeria</h3>
                    <p class="text-xs text-lavender/50 leading-relaxed font-manrope">A registered Nigerian web design company delivering enterprise-grade digital solutions at competitive, transparent rates.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- TESTIMONIALS                            -->
    <!-- ═══════════════════════════════════════ -->
    <section class="py-24 md:py-40 px-4 md:px-8 bg-matte-black border-t border-lavender/10 relative z-20 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] rounded-full pointer-events-none" style="background: radial-gradient(ellipse, rgba(126,34,206,0.06) 0%, transparent 70%);"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-5">[ Client Testimonials ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white leading-tight mb-4">
                    Verified voices. Businesses that trusted the process.
                </h2>
                <p class="font-manrope text-lavender/60 text-lg">From Osogbo to Ilorin, Enugu to Abuja   and beyond Nigeria's borders.</p>
            </div>

            <div class="flex md:block overflow-x-auto md:overflow-visible snap-x snap-mandatory hide-scrollbar gap-6 pb-8 md:pb-0 md:columns-2 xl:columns-3 md:space-y-8 px-1 -mx-1"
                 style="padding-left: max(0.25rem, env(safe-area-inset-left)); padding-right: max(0.25rem, env(safe-area-inset-right));">

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">Business Consulting</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>Faith made the process simple. He doesn't just build pages; he builds solutions. He turned our concept into a working prototype quickly and integrated AI features that are already helping our business. I can't recommend them enough.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#0f6e56;">OI</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Olayinka Itunu Damilare</p>
                            <p class="font-manrope text-xs text-lavender/50">CEO, De Kompany Consulting Services</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">Ilorin, Kwara State</p>
                        </div>
                    </div>
                </div>

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">Web & Mobile App</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>GetOnline Studio developed a website and mobile app for us, and we couldn't be happier with the result. They made the whole process easy and delivered exactly what we needed. Truly the best web designer in Osogbo, Osun State.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#185fa5;">MK</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Mr. Mike</p>
                            <p class="font-manrope text-xs text-lavender/50">Technical Director, RaffleKings</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">Nigeria</p>
                        </div>
                    </div>
                </div>

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">International NGO</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>As an international organization, we required a digital presence that reflected our authority. GetOnline Studio proved to be a highly trusted partner. The speed of delivery did not compromise quality. We highly recommend them for any institution seeking world-class web development.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#7e22ce;">JA</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Amb. Dr. Jernail Singh Anand</p>
                            <p class="font-manrope text-xs text-lavender/50">World Foundation for Peace</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">India</p>
                        </div>
                    </div>
                </div>

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">Education & Academy</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>GetOnline Studio delivered more than just a system. They transformed how we operate. They built a custom online academy where students learn at their own pace, engage with lecturers, track progress, and connect with peers.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#3b6d11;">EA</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Emmanuel Amaechi</p>
                            <p class="font-manrope text-xs text-lavender/50">Academic Director, Peace Academy</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">Enugu Branch</p>
                        </div>
                    </div>
                </div>

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">Leadership & Governance</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>I proudly commend GetOnline Studio for their exceptional quality and reliable services. Their professionalism, creativity, and attention to detail consistently set them apart. Their ability to understand clients' needs and translate them into functional, elegant digital solutions is truly admirable.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#854f0b;">LK</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Chief Lamina Kamiludeen Omotoyosi</p>
                            <p class="font-manrope text-xs text-lavender/50">Executive Director, World Institute for Peace</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">Abuja</p>
                        </div>
                    </div>
                </div>

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">Fintech & Microfinance</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>Working with GetOnline Studio on our microfinance app was an excellent experience. He demonstrated a high level of professionalism and a clear understanding of our business needs. He consistently delivered beyond expectations.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#993556;">TB</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Tobiloba Babalola</p>
                            <p class="font-manrope text-xs text-lavender/50">MD, OA Global Standard Services</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">Osogbo</p>
                        </div>
                    </div>
                </div>

                <div class="snap-center min-w-[85vw] sm:min-w-[350px] md:min-w-0 break-inside-avoid bg-card-dark border border-lavender/10 rounded-2xl p-7 hover:border-sharp-purple/30 transition-all duration-300 flex flex-col gap-5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-yellow-400 text-sm">★★★★★</span>
                        <span class="font-mono text-[9px] uppercase tracking-widest text-lavender/30 border border-lavender/10 px-2 py-1 rounded-full">Church & Non-profit</span>
                    </div>
                    <blockquote class="font-manrope text-sm text-lavender/80 leading-relaxed flex-1">
                        <span class="text-3xl text-sharp-purple/40 font-serif leading-none mr-1 align-bottom">&ldquo;</span>I needed help designing a website for our church so I was referred to them. They really met our expectations and our church vision. Good work.
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-lavender/5">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-syne font-bold text-xs text-white" style="background-color:#7e22ce;">OS</div>
                        <div>
                            <p class="font-syne font-bold text-sm text-white">Mr. Ogundeji Sinmisola</p>
                            <p class="font-manrope text-xs text-lavender/50">Church Administrator</p>
                            <p class="font-mono text-[9px] text-lavender/30 uppercase tracking-widest mt-0.5">Ogbomosho, Oyo State</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-12 text-center">
                <a href="/testimonials/" class="inline-flex items-center gap-2 border border-lavender/20 text-lavender/70 hover:text-white hover:border-sharp-purple/50 px-8 py-3 rounded-full text-sm font-bold uppercase tracking-widest transition-all hover-target">
                    Read All Reviews <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- WHY CHOOSE US                           -->
    <!-- ═══════════════════════════════════════ -->
    <section class="py-20 md:py-32 px-4 md:px-8 bg-[#0a0a0a] border-t border-lavender/10 z-20 relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Why GetOnline Studio ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white max-w-3xl mx-auto">Why hundreds of Nigerian businesses choose us over every other web design firm.</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-matte-black border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="shield-check" class="w-8 h-8 text-sharp-purple mb-6"></i>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">We Are Business-Minded First</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We don't just make things look pretty. Every decision we make   design, copy, structure   is driven by one question: will this make our client more money or lose them less?</p>
                </div>
                <div class="bg-matte-black border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="zap" class="w-8 h-8 text-sharp-purple mb-6"></i>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Fast Delivery Without Cutting Corners</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">Most projects ship in 7 to 14 days. Not months. We have built workflows that let us move quickly while maintaining the quality standards that our international clients expect.</p>
                </div>
                <div class="bg-matte-black border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="globe" class="w-8 h-8 text-sharp-purple mb-6"></i>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Nigeria-Based, World-Class Standard</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We are a web design company rooted in Nigeria, but our work has impressed clients in India, the UK, and across Africa. We combine local understanding with global execution standards.</p>
                </div>
                <div class="bg-matte-black border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="users" class="w-8 h-8 text-sharp-purple mb-6"></i>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Full In-House Team</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">No freelancers. No handoffs. Your project is handled by our own web designers, web developers, and strategists from start to finish. This means accountability and consistency throughout.</p>
                </div>
                <div class="bg-matte-black border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="trending-up" class="w-8 h-8 text-sharp-purple mb-6"></i>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">SEO Built Into Everything</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We don't build a website and then add SEO as an afterthought. Every platform we build has Local SEO architecture, proper schema markup, fast loading, and Google-ready structure from the ground up.</p>
                </div>
                <div class="bg-matte-black border border-lavender/10 rounded-2xl p-8 hover:border-sharp-purple/40 transition-colors">
                    <i data-lucide="heart-handshake" class="w-8 h-8 text-sharp-purple mb-6"></i>
                    <h3 class="font-syne text-xl font-bold text-white mb-3">Transparent, No Surprises</h3>
                    <p class="font-manrope text-sm text-lavender/60 leading-relaxed">We quote clearly. We communicate regularly. You always know where your project stands, what has been done, and what is next. No hidden fees, no radio silence, no excuses.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FAQ                                     -->
    <!-- ═══════════════════════════════════════ -->
    <section class="relative py-20 md:py-32 px-4 md:px-8 bg-matte-black border-t border-lavender/10 z-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-20">
            <div>
                <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-6">[ FAQ ]</p>
                <h2 class="font-syne text-3xl md:text-5xl font-bold text-white leading-tight mb-6">
                    Questions people ask our web design firm.
                </h2>
                <p class="font-manrope text-lavender/60 text-sm leading-relaxed mb-8">
                    Still have questions about working with us? Every question below is one we have actually been asked by Nigerian business owners. If yours is not here, just send us a message.
                </p>
                <a href="/contact" class="inline-flex items-center gap-2 border border-lavender/30 px-6 py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-lavender hover:text-matte-black transition-all hover-target">
                    Ask Us Directly <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>

            <div class="col-span-1 lg:col-span-2 flex flex-col">

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">How much does a website cost in Nigeria?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            Website costs in Nigeria vary based on what you actually need. A clean, professional business website typically starts from <strong class="text-white">₦120,000</strong>. A more powerful platform   with booking systems, payment integration, or custom portals   ranges from <strong class="text-white">₦250,000 to ₦600,000+</strong>. Enterprise systems like web apps and mobile applications are quoted separately. We give you an accurate quote after a free discovery call, so there are no surprises.
                        </p>
                    </div>
                </div>

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">What is the difference between a web designer, web developer, and web design agency?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            A <strong class="text-white">web designer</strong> handles how your site looks   layout, colours, typography, and user experience. A <strong class="text-white">web developer</strong> handles how it works   writing the code, building the backend, and connecting databases and APIs. A <strong class="text-white">web design agency</strong> like GetOnline Studio is both under one roof, plus strategy, SEO, branding, and ongoing support. When you work with us, you get the full picture, not just one piece of it.
                        </p>
                    </div>
                </div>

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">How long does it take to build a website?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            A standard business website takes <strong class="text-white">7 to 14 days</strong> from the time we receive your content and information. Custom web portals, e-commerce platforms, and web apps take <strong class="text-white">3 to 8 weeks</strong> depending on complexity. Mobile apps take longer. We always give you a clear, agreed timeline before we start   and we stick to it. Delays are usually caused by missing content from the client's side, so we guide you through exactly what we need upfront.
                        </p>
                    </div>
                </div>

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">Do you work with clients outside Nigeria?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            Yes. We are a digital-first web design company, which means location is not a barrier. We have delivered projects for clients in <strong class="text-white">India, the United Kingdom, the United States</strong>, and across Africa. Our project communication, file sharing, and review processes are all structured for remote collaboration. Time zone differences have never caused a project delay.
                        </p>
                    </div>
                </div>

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">Will my website rank on Google in Nigeria?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            Every website we build comes with <strong class="text-white">on-page SEO architecture</strong> built in from the start   proper headings, meta descriptions, schema markup, fast loading, and mobile optimisation. For serious local rankings, we also offer dedicated <strong class="text-white">Local SEO packages</strong> that target specific cities and include Google Business Profile setup and optimisation. SEO takes time, but starting with a properly built site puts you miles ahead of competitors who don't.
                        </p>
                    </div>
                </div>

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">Do I need a website if I already have Instagram and WhatsApp?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            Yes. Instagram and WhatsApp are tools for engagement. A website is your <strong class="text-white">digital headquarters</strong>   the only platform you fully own and control. Instagram can suspend your account tomorrow. WhatsApp is not searchable on Google. A professional website builds trust, shows up in search results, works 24/7, and positions you as a serious business. Most clients who find you online will check your website before they contact you. If there is no website, many simply move on to your competitor.
                        </p>
                    </div>
                </div>

                <div class="faq-item border-b border-lavender/10 py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">Is GetOnline Studio a registered company in Nigeria?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            Yes. GetOnline Studio is a <strong class="text-white">registered digital agency in Nigeria</strong>. We are a web design firm you can trust with your business information, brand assets, and investment. We also help other businesses get their own CAC registration as part of our services   so you can work with a team that has been through the process themselves.
                        </p>
                    </div>
                </div>

                <div class="faq-item py-6 cursor-pointer group hover-target">
                    <div class="flex justify-between items-center">
                        <h3 class="font-syne text-lg md:text-2xl group-hover:text-sharp-purple transition-colors pr-6">What happens after my website is launched?</h3>
                        <span class="faq-icon text-2xl text-sharp-purple flex-shrink-0">+</span>
                    </div>
                    <div class="faq-answer mt-4">
                        <p class="font-manrope text-lavender/70 text-sm md:text-base leading-relaxed">
                            Launch is not the end   it is the beginning. We offer <strong class="text-white">website maintenance plans</strong> that cover security updates, backups, speed optimisation, and priority support. Many clients also continue with us for SEO, social media strategy, and business automation setup after their site goes live. We are not the kind of web design agency that disappears once they collect payment. We build long-term relationships because that is where the real results come from.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- LOCATIONS COVERAGE                      -->
    <!-- ═══════════════════════════════════════ -->
    <section class="py-16 px-4 md:px-8 bg-[#0a0a0a] border-t border-lavender/10 z-20 relative">
        <div class="max-w-7xl mx-auto">
            <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-6">[ We Serve Every City in Nigeria ]</p>
            <div class="flex flex-wrap gap-3">
                <a href="/locations/lagos/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Lagos</a>
                <a href="/locations/abuja/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Abuja</a>
                <a href="/locations/port-harcourt/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Port Harcourt</a>
                <a href="/locations/ibadan/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Ibadan</a>
                <a href="/locations/kano/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Kano</a>
                <a href="/locations/enugu/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Enugu</a>
                <a href="/locations/benin-city/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Benin City</a>
                <a href="/locations/ilorin/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Ilorin</a>
                <a href="/locations/osogbo/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Osogbo</a>
                <a href="/locations/aba/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Aba</a>
                <a href="/locations/warri/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Warri</a>
                <a href="/locations/abeokuta/" class="font-manrope text-sm text-lavender/50 hover:text-lavender border border-lavender/10 hover:border-lavender/30 px-4 py-2 rounded-full transition-all hover-target">Abeokuta</a>
                <a href="/locations/" class="font-manrope text-sm text-sharp-purple hover:text-white border border-sharp-purple/30 hover:border-white/30 px-4 py-2 rounded-full transition-all hover-target font-bold">All Locations →</a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FINAL CTA                               -->
    <!-- ═══════════════════════════════════════ -->
    <section id="contact" class="py-32 md:py-40 px-4 md:px-8 text-center bg-sharp-purple text-white relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-noise mix-blend-overlay opacity-10 pointer-events-none"></div>
        <div class="max-w-4xl mx-auto relative z-10">
            <p class="font-mono text-[10px] text-white/60 tracking-widest uppercase mb-6">[ Ready to Get Online? ]</p>
            <h2 class="font-syne text-4xl md:text-7xl font-bold mb-4 text-white leading-tight">
                Let's build something that works.
            </h2>
            <p class="font-manrope text-lg md:text-xl mb-12 text-white/80 max-w-2xl mx-auto leading-relaxed">
                Every day without a professional website is a day a potential customer chose your competitor. Our discovery call is free and takes 20 minutes. Let's talk.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="mailto:hello@getonlinestudio.com?subject=Website%20Project%20Inquiry" class="w-full sm:w-auto bg-matte-black text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-white hover:text-matte-black transition-all hover-target shadow-2xl">
                    Email Us →
                </a>
                <a href="https://wa.me/2349061150443?text=Hi%20GetOnline%20Studio%2C%20I%20want%20to%20discuss%20a%20project!" target="_blank" class="w-full sm:w-auto bg-[#25D366] text-white px-10 py-5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-[#1ebe5d] transition-all hover-target shadow-xl flex items-center justify-center gap-2">
                    <i data-lucide="message-circle" class="w-5 h-5"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FOOTER (Rich)                           -->
    <!-- ═══════════════════════════════════════ -->
    <footer class="bg-[#0a0a0a] border-t border-lavender/10 relative z-20 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(126,34,206,0.07),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(37,211,102,0.04),transparent_25%)] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 md:px-8 relative">

            <!-- Top: Big CTA + About -->
            <div class="py-16 md:py-20 grid lg:grid-cols-2 gap-12 border-b border-lavender/10">
                <div>
                    <h2 class="font-syne text-5xl md:text-7xl font-bold text-lavender leading-none mb-5">LET'S<br>TALK.</h2>
                    <p class="font-manrope text-lavender/60 text-sm md:text-base leading-relaxed max-w-md mb-6">
                        Ready to get more customers online? Our team is available right now. We respond fast   usually within a few hours on working days.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="https://wa.me/2349061150443" target="_blank" class="inline-flex items-center gap-2 bg-[#25D366] text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-[#1ebe5d] transition-all hover-target">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> +234 906 115 0443
                        </a>
                        <a href="mailto:hello@getonlinestudio.com" class="inline-flex items-center gap-2 text-sharp-purple font-bold text-sm hover:text-white transition-colors hover-target border border-sharp-purple/30 px-6 py-3 rounded-full hover:border-white/30">
                            <i data-lucide="mail" class="w-4 h-4"></i> hello@getonlinestudio.com
                        </a>
                    </div>
                </div>
                <div class="lg:pt-4">
                    <p class="font-mono text-[10px] text-sharp-purple tracking-widest uppercase mb-4">[ Who We Are ]</p>
                    <h3 class="font-syne text-2xl font-bold text-white mb-4">GetOnline Studio</h3>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed mb-4">
                        We are a Nigerian web design agency and digital infrastructure company helping businesses, brands, and organisations build a serious online presence. From professional websites and SEO to branding, mobile apps, automation, and CAC registration   we give your business everything it needs to get found, look credible, and grow.
                    </p>
                    <p class="font-manrope text-lavender/60 text-sm leading-relaxed">
                        We work with businesses across every major city in Nigeria and internationally. Whether you are just starting or have been in business for years, we will help you get the digital presence you deserve.
                    </p>
                </div>
            </div>

            <!-- Middle: Nav Links -->
            <div class="py-12 grid grid-cols-2 md:grid-cols-4 gap-8 border-b border-lavender/10">
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Company</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/" class="text-lavender/60 hover:text-white transition-colors hover-target">Home</a></li>
                        <li><a href="/about/" class="text-lavender/60 hover:text-white transition-colors hover-target">About Us</a></li>
                        <li><a href="/work/" class="text-lavender/60 hover:text-white transition-colors hover-target">Projects & Case Studies</a></li>
                        <li><a href="/testimonials/" class="text-lavender/60 hover:text-white transition-colors hover-target">Testimonials</a></li>
                        <li><a href="/blog/" class="text-lavender/60 hover:text-white transition-colors hover-target">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Services</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/services/web-design/" class="text-lavender/60 hover:text-white transition-colors hover-target">Website Design</a></li>
                        <li><a href="/services/development/" class="text-lavender/60 hover:text-white transition-colors hover-target">Web Development</a></li>
                        <li><a href="/services/seo/" class="text-lavender/60 hover:text-white transition-colors hover-target">SEO & Google Ranking</a></li>
                        <li><a href="/services/branding/" class="text-lavender/60 hover:text-white transition-colors hover-target">Branding & Identity</a></li>
                        <li><a href="/services/" class="text-lavender/60 hover:text-sharp-purple transition-colors hover-target font-bold">All Services →</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Locations</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="/locations/lagos/" class="text-lavender/60 hover:text-white transition-colors hover-target">Web Designer Lagos</a></li>
                        <li><a href="/locations/abuja/" class="text-lavender/60 hover:text-white transition-colors hover-target">Web Designer Abuja</a></li>
                        <li><a href="/locations/port-harcourt/" class="text-lavender/60 hover:text-white transition-colors hover-target">Web Designer Port Harcourt</a></li>
                        <li><a href="/locations/ibadan/" class="text-lavender/60 hover:text-white transition-colors hover-target">Web Designer Ibadan</a></li>
                        <li><a href="/locations/" class="text-lavender/60 hover:text-sharp-purple transition-colors hover-target font-bold">All Locations →</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-mono text-[10px] text-lavender/30 tracking-widest uppercase mb-5">Connect</p>
                    <ul class="space-y-3 font-manrope text-sm">
                        <li><a href="https://wa.me/2349061150443" target="_blank" class="text-lavender/60 hover:text-white transition-colors hover-target">WhatsApp</a></li>
                        <li><a href="https://instagram.com/getonlinestudio" target="_blank" class="text-lavender/60 hover:text-white transition-colors hover-target">Instagram</a></li>
                        <li><a href="https://twitter.com/getonlinestudio" target="_blank" class="text-lavender/60 hover:text-white transition-colors hover-target">Twitter / X</a></li>
                        <li><a href="https://linkedin.com/company/getonlinestudio" target="_blank" class="text-lavender/60 hover:text-white transition-colors hover-target">LinkedIn</a></li>
                        <li><a href="/contact/" class="text-lavender/60 hover:text-white transition-colors hover-target">Contact Page</a></li>
                    </ul>
                </div>
            </div>

            <!-- SEO keyword footer links -->
            <div class="py-8 border-b border-lavender/10">
                <p class="font-mono text-[10px] text-lavender/20 tracking-widest uppercase mb-4">Web Design Services in Nigeria</p>
                <div class="flex flex-wrap gap-3">
                    <a href="/locations/lagos/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Design Agency Lagos</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/abuja/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Design Company Abuja</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/port-harcourt/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Designer Port Harcourt</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/ibadan/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Design Firm Ibadan</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/enugu/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Developer Enugu</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/kano/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Design Services Kano</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/benin-city/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Designer Benin City</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/ilorin/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Design Agency Ilorin</a>
                    <span class="text-lavender/10">·</span>
                    <a href="/locations/osogbo/" class="text-[10px] text-lavender/30 hover:text-lavender/60 transition-colors hover-target">Web Developer Osogbo</a>
                </div>
            </div>

            <!-- Legal -->
            <div class="py-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="font-manrope text-xs text-lavender/30">GetOnline Studio &copy; 2026. Web Design Agency Nigeria. <a href="/locations/" class="underline decoration-sharp-purple hover:text-sharp-purple transition-colors">Proudly serving every city in Nigeria.</a></p>
                <div class="flex gap-6 font-manrope text-xs text-lavender/30">
                    <a href="/privacy-policy/" class="hover:text-white transition-colors hover-target">Privacy Policy</a>
                    <a href="/terms-of-service/" class="hover:text-white transition-colors hover-target">Terms of Service</a>
                    <a href="/cookie-policy/" class="hover:text-white transition-colors hover-target">Cookie Policy</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- Cookie Consent -->
    <div id="cookie-banner" class="fixed bottom-4 md:bottom-6 right-4 md:right-6 max-w-xs w-[calc(100%-2rem)] z-50 transform translate-y-20 opacity-0 transition-all duration-700 pointer-events-none">
        <div class="bg-card-dark/95 backdrop-blur-md border border-lavender/10 p-5 rounded-xl shadow-2xl">
            <h4 class="font-syne text-sharp-purple text-xs font-bold uppercase tracking-widest mb-2">( COOKIES )</h4>
            <p class="font-manrope text-xs text-lavender/70 mb-4 leading-relaxed">
                We use cookies to ensure you get the best experience. <a href="/privacy-policy" class="text-white underline decoration-sharp-purple underline-offset-4 hover:text-sharp-purple transition-colors">Read Policy</a>.
            </p>
            <div class="flex gap-3">
                <button id="accept-cookies" class="flex-1 bg-lavender text-matte-black font-syne font-bold text-[10px] uppercase tracking-widest py-2.5 rounded-lg hover:bg-white transition-colors cursor-pointer hover-target">Accept</button>
                <button id="decline-cookies" class="flex-1 border border-lavender/20 text-lavender font-syne font-bold text-[10px] uppercase tracking-widest py-2.5 rounded-lg hover:bg-lavender/10 transition-colors cursor-pointer hover-target">Decline</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // Menu
        const menu = document.getElementById('mobile-menu');
        document.getElementById('open-menu').addEventListener('click', () => {
            menu.classList.add('open'); document.body.style.overflow = 'hidden';
        });
        document.getElementById('close-menu').addEventListener('click', () => {
            menu.classList.remove('open'); document.body.style.overflow = '';
        });

        // Cursor
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
            const bindHover = () => {
                document.querySelectorAll('.hover-target, a, button, .faq-item').forEach(el => {
                    if (!el.dataset.cursorBound) {
                        el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
                        el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
                        el.dataset.cursorBound = true;
                    }
                });
            };
            bindHover();
        }

        // Kinetic scroll - desktop only
        if (!isTouchDevice) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            document.querySelectorAll('.kinetic-text').forEach(el => {
                const speed = el.getAttribute('data-speed');
                el.style.transform = `translateX(${scrolled * speed}px)`;
            });
        });
        }

        // FAQ
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                if (!isActive) item.classList.add('active');
            });
        });

        // Cookie
        const cookieBanner = document.getElementById('cookie-banner');
        if (!localStorage.getItem('cookieConsent')) {
            setTimeout(() => cookieBanner.classList.remove('translate-y-20','opacity-0','pointer-events-none'), 3500);
        }
        document.getElementById('accept-cookies').addEventListener('click', () => {
            localStorage.setItem('cookieConsent','accepted');
            cookieBanner.classList.add('translate-y-20','opacity-0','pointer-events-none');
        });
        document.getElementById('decline-cookies').addEventListener('click', () => {
            localStorage.setItem('cookieConsent','declined');
            cookieBanner.classList.add('translate-y-20','opacity-0','pointer-events-none');
        });

        // Intro cleanup
        setTimeout(() => {
            const overlay = document.getElementById('intro-overlay');
            if (overlay) overlay.style.pointerEvents = 'none';
        }, 3500);
    </script>
<?php wp_footer(); ?>
</body>
</html>