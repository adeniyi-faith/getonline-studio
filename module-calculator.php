<?php
/**
 * DYNAMIC PRICING CALCULATOR MODULE (JSON CONNECTED + ADVANCED WA ROUTING)
 * Include this inside niche-landing.php
 * Usage: include 'module-calculator.php';
 * * Assumes $city_name, $niche_name, $niche_plural, $cost_low, $cost_typical, $cost_high, and $vocab are already defined in parent.
 */

// 1. Fetch Niche-Specific Meta First (Needed for Features list)
$niche_post = get_page_by_path($niche_slug, OBJECT, 'pseo_niche');
$niche_meta = [];
$priorities = 'mobile responsiveness, fast load times, clear service pages, and direct WhatsApp contact buttons'; // Default

if ($niche_post) {
    $niche_meta = get_post_meta($niche_post->ID, '_pseo_niche_data', true);
    if (!empty($niche_meta['typical_features']) && is_array($niche_meta['typical_features'])) {
        // Convert the array of features into a comma-separated string
        $priorities = implode(', ', $niche_meta['typical_features']);
    }
}

// 2. Inherit Real Pricing Data from the Parent File (niche-landing.php JSON Engine)
$base_price = isset($cost_low) ? $cost_low : 120000;
$typical_price = isset($cost_typical) ? $cost_typical : 250000;
$high_price = isset($cost_high) ? $cost_high : 450000;

// Resolve organization label based on Niche Tone Engine
$org_label = isset($vocab['org_label']) ? $vocab['org_label'] : 'businesses';

// Calculate Dynamic Support Fee
$support_fee = ($base_price < 100000) ? 40000 : 45000;

// Format numbers for display
$fmt_base = '₦' . number_format($base_price);
$fmt_typical = '₦' . number_format($typical_price);
$fmt_high = '₦' . number_format($high_price);
$fmt_support = '₦' . number_format($support_fee);
?>

<!-- PRICING & CALCULATOR SECTION -->
<section id="pricing-calculator" class="py-24 bg-[#0B0A0F] border-t border-white/5 relative overflow-hidden">
    <!-- Background Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-sharp-purple/10 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        
        <!-- Contextual Header (Layer 3 Differentiation) -->
        <div class="max-w-3xl mx-auto text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sharp-purple/10 border border-sharp-purple/30 text-sharp-purple text-xs font-bold uppercase tracking-wider mb-6">
                <i data-lucide="calculator" class="w-4 h-4"></i> Pricing Intelligence
            </div>
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sharp-purple to-lavender"><?= esc_html($niche_name) ?></span> Website Design Cost in <?= esc_html($city_name) ?>
            </h2>
            <p class="text-lg text-lavender/70 leading-relaxed mb-4">
                The average cost of a professional <?= esc_html(strtolower($niche_name)) ?> website in <?= esc_html($city_name) ?> ranges from <strong class="text-white"><?= $fmt_base ?> to <?= $fmt_high ?></strong>, with most <?= esc_html($org_label) ?> investing around <strong class="text-white"><?= $fmt_typical ?></strong> for a complete professional presence.
            </p>
            <p class="text-md text-lavender/50 leading-relaxed">
                <?= esc_html($city_name) ?> <?= esc_html($niche_plural) ?> typically prioritize: <span class="text-white"><?= esc_html($priorities) ?></span> — reflecting the city's specific market demands.
            </p>
        </div>

        <!-- Interactive Calculator Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-5xl mx-auto">
            
            <!-- Toggles / Inputs -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-panel-dark border border-white/5 rounded-2xl p-6 md:p-8">
                    <h3 class="text-xl font-bold text-white mb-6">Customize Your Setup</h3>
                    
                    <div class="space-y-3" id="calc-options">
                        <!-- Option 1 -->
                        <label class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/5 cursor-pointer hover:bg-white/10 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-sharp-purple transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="0" checked disabled>
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name">Base Professional Site</div>
                                    <div class="text-lavender/50 text-xs">Up to 5 pages, mobile-optimized, fast load</div>
                                </div>
                            </div>
                            <div class="text-sharp-purple font-mono text-sm font-bold">Included</div>
                        </label>

                        <!-- Option 2 -->
                        <label class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-black/20 cursor-pointer hover:bg-white/5 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-sharp-purple transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="45000">
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple opacity-0 transition-opacity"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name">Advanced SEO Setup</div>
                                    <div class="text-lavender/50 text-xs">Keyword optimization & Google Maps setup</div>
                                </div>
                            </div>
                            <div class="text-lavender/50 font-mono text-sm">+₦45,000</div>
                        </label>

                        <!-- Option 3 -->
                        <label class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-black/20 cursor-pointer hover:bg-white/5 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-sharp-purple transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="65000">
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple opacity-0 transition-opacity"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name">Booking / Contact System</div>
                                    <div class="text-lavender/50 text-xs">Automated appointments & secure forms</div>
                                </div>
                            </div>
                            <div class="text-lavender/50 font-mono text-sm">+₦65,000</div>
                        </label>

                        <!-- Option 4 -->
                        <label class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-black/20 cursor-pointer hover:bg-white/5 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-sharp-purple transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="35000">
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple opacity-0 transition-opacity"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name">Professional Copywriting</div>
                                    <div class="text-lavender/50 text-xs">We write high-converting text for your pages</div>
                                </div>
                            </div>
                            <div class="text-lavender/50 font-mono text-sm">+₦35,000</div>
                        </label>
                        
                        <!-- NEW Option: Support & Maintenance (Auto-Checked) -->
                        <label class="flex items-center justify-between p-4 rounded-xl border border-sharp-purple/50 bg-sharp-purple/10 cursor-pointer hover:bg-white/5 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="relative flex items-center justify-center w-6 h-6 rounded border border-lavender/30 bg-black/50 group-hover:border-sharp-purple transition-colors">
                                    <input type="checkbox" class="calc-checkbox absolute opacity-0 w-full h-full cursor-pointer" data-price="<?= $support_fee ?>" checked>
                                    <i data-lucide="check" class="w-4 h-4 text-sharp-purple transition-opacity"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm feature-name">1-Year Support & Maintenance</div>
                                    <div class="text-lavender/50 text-xs">Security updates, backups, and priority support</div>
                                </div>
                            </div>
                            <div class="text-lavender/50 font-mono text-sm">+<?= $fmt_support ?></div>
                        </label>

                        <!-- Custom Needs Field -->
                        <div class="pt-4 mt-2 border-t border-white/5">
                            <label class="block text-white font-bold text-sm mb-2">Need something else not listed?</label>
                            <textarea id="calc-custom-needs" rows="2" class="w-full bg-black/30 border border-lavender/20 rounded-xl p-4 text-lavender text-sm focus:outline-none focus:border-sharp-purple transition-colors resize-none placeholder:text-lavender/30" placeholder="E.g., I also need a membership portal, WhatsApp chatbot, specific payment gateway..."></textarea>
                            <p class="text-[10px] text-lavender/40 mt-1 uppercase tracking-widest">Pricing for custom features will be discussed directly.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Total Box -->
            <div class="lg:col-span-5">
                <div class="bg-gradient-to-b from-sharp-purple to-[#4c1d95] rounded-2xl p-1 lg:sticky lg:top-24">
                    <div class="bg-panel-dark rounded-xl p-8 h-full flex flex-col justify-between">
                        <div>
                            <div class="text-lavender/70 font-bold text-sm uppercase tracking-wider mb-2">Estimated Investment</div>
                            <div class="text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tighter flex items-center gap-1">
                                ₦<span id="calc-total" data-base="<?= $base_price ?>"><?= number_format($base_price) ?></span>
                            </div>
                            
                            <ul class="space-y-3 mb-8" id="active-features-list">
                                <li class="flex items-start gap-2 text-sm text-lavender/80">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-sharp-purple mt-0.5 shrink-0"></i>
                                    <span>Base <?= esc_html($niche_name) ?> Setup</span>
                                </li>
                                <!-- JS will inject active features here -->
                            </ul>
                        </div>

                        <!-- 2348108275013 is your number mapped from niche-landing.php -->
                        <a href="#" 
                           id="calc-whatsapp-btn"
                           target="_blank"
                           class="w-full flex items-center justify-center gap-2 bg-sharp-purple text-white px-6 py-4 rounded-xl font-bold hover:scale-[1.02] hover:shadow-[0_0_20px_rgba(126,34,206,0.4)] transition-all">
                            Discuss This Project <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Calculator Logic -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.calc-checkbox');
        const customNeedsInput = document.getElementById('calc-custom-needs');
        const totalDisplay = document.getElementById('calc-total');
        const featuresList = document.getElementById('active-features-list');
        const whatsappBtn = document.getElementById('calc-whatsapp-btn');
        const basePrice = parseInt(totalDisplay.getAttribute('data-base'));
        const phoneNumber = "2348108275013"; // Your GetOnline Studio Number

        // Variables from PHP for the WhatsApp Text
        const nicheName = "<?= esc_js($niche_name) ?>";
        const cityName = "<?= esc_js($city_name) ?>";

        // Number animation function
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const currentVal = Math.floor(progress * (end - start) + start);
                obj.innerHTML = currentVal.toLocaleString('en-US');
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    obj.innerHTML = end.toLocaleString('en-US'); // Ensure exact final value
                }
            };
            window.requestAnimationFrame(step);
        }

        function calculateTotal() {
            let newTotal = basePrice;
            let activeFeaturesHTML = `<li class="flex items-start gap-2 text-sm text-lavender/80">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-sharp-purple mt-0.5 shrink-0"></i>
                                        <span>Base ${nicheName} Setup</span>
                                      </li>`;
            
            // Array to store selected feature strings for WhatsApp
            let waSelectedFeatures = [`Base ${nicheName} Setup`];

            checkboxes.forEach(box => {
                const icon = box.nextElementSibling;
                const parentLabel = box.closest('label');
                const featureName = parentLabel.querySelector('.feature-name').innerText;

                if (box.checked) {
                    newTotal += parseInt(box.getAttribute('data-price') || 0);
                    
                    if (!box.disabled) {
                        icon.classList.remove('opacity-0');
                        parentLabel.classList.add('border-sharp-purple/50', 'bg-sharp-purple/10');
                        parentLabel.classList.remove('border-white/5', 'bg-black/20');
                    }
                    
                    if(box.getAttribute('data-price') > 0) {
                        activeFeaturesHTML += `<li class="flex items-start gap-2 text-sm text-lavender/80">
                                                <i data-lucide="check-circle-2" class="w-4 h-4 text-sharp-purple mt-0.5 shrink-0"></i>
                                                <span>${featureName}</span>
                                              </li>`;
                        waSelectedFeatures.push(featureName);
                    }
                } else {
                    if (!box.disabled) {
                        icon.classList.add('opacity-0');
                        parentLabel.classList.remove('border-sharp-purple/50', 'bg-sharp-purple/10');
                        parentLabel.classList.add('border-white/5', 'bg-black/20');
                    }
                }
            });

            // Animate to new total
            const currentDisplayed = parseInt(totalDisplay.innerHTML.replace(/,/g, ''));
            animateValue(totalDisplay, currentDisplayed, newTotal, 400);

            // Update UI List
            featuresList.innerHTML = activeFeaturesHTML;
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Construct highly-qualified WhatsApp Message
            const customNeeds = customNeedsInput.value.trim();
            
            let waMessage = `Hi GetOnline Studio,\n\nI am looking to build a ${nicheName} website in ${cityName}.\n\nBased on your calculator, my estimated budget is ₦${newTotal.toLocaleString('en-US')}.\n\n*Features I selected:*\n- ${waSelectedFeatures.join('\n- ')}`;

            if (customNeeds !== "") {
                waMessage += `\n\n*Additional Custom Requirements:*\n${customNeeds}`;
            }

            waMessage += `\n\nLet's discuss how we can get this project started.`;

            // Encode for URL and set href
            const newLink = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(waMessage)}`;
            whatsappBtn.setAttribute('href', newLink);
        }

        // Add Event Listeners
        checkboxes.forEach(box => {
            if(!box.disabled) {
                box.addEventListener('change', calculateTotal);
            }
        });

        // Update WhatsApp link live as they type custom requirements
        customNeedsInput.addEventListener('input', calculateTotal);

        // Run once on load to establish the initial link state
        calculateTotal();
    });
</script>