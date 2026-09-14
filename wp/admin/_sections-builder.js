/**
 * Lightweight "add a block" content builder shared by the Portfolio and
 * Site Page admin forms. No build step, no framework — just enough to
 * let an admin assemble a page out of a few block types without ever
 * touching a template file.
 *
 * Usage (see portfolio.php / pages.php for the exact markup expected):
 *   GoSectionsBuilder.init({
 *     listEl: document.getElementById('sections-list'),
 *     hiddenInputEl: document.getElementById('sections-json'),
 *     formEl: document.getElementById('content-form'),
 *     initial: [...]   // array of existing block objects
 *   });
 */
(function (window) {
    const BLOCK_LABELS = {
        heading: 'Heading',
        paragraph: 'Paragraph',
        image: 'Image',
        quote: 'Quote',
        stat_row: 'Stat row',
        faq: 'FAQ group',
    };

    function el(tag, className, html) {
        const e = document.createElement(tag);
        if (className) e.className = className;
        if (html !== undefined) e.innerHTML = html;
        return e;
    }

    function fieldsFor(type, data) {
        data = data || {};
        switch (type) {
            case 'heading':
                return `<label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">Heading text</label>
                    <input type="text" data-field="text" value="${escAttr(data.text)}" class="go-input" placeholder="Why it matters">`;
            case 'paragraph':
                return `<label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">Paragraph</label>
                    <textarea data-field="text" rows="4" class="go-input" placeholder="Write the paragraph text...">${escHtml(data.text)}</textarea>`;
            case 'image':
                return `<label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">Image URL</label>
                    <input type="url" data-field="url" value="${escAttr(data.url)}" class="go-input mb-3" placeholder="https://...">
                    <label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">Caption (optional)</label>
                    <input type="text" data-field="caption" value="${escAttr(data.caption)}" class="go-input">`;
            case 'quote':
                return `<label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">Quote text</label>
                    <textarea data-field="text" rows="2" class="go-input mb-3" placeholder="The quote itself...">${escHtml(data.text)}</textarea>
                    <label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">Attribution (optional)</label>
                    <input type="text" data-field="attribution" value="${escAttr(data.attribution)}" class="go-input" placeholder="— Jane Doe, CEO">`;
            case 'stat_row':
                return `<label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">One stat per line, as "value | label"</label>
                    <textarea data-field="stats_text" rows="4" class="go-input" placeholder="50+ | Projects delivered&#10;8 | Years in business">${escHtml(statsToText(data.stats))}</textarea>`;
            case 'faq':
                return `<label class="block text-xs uppercase tracking-widest text-lavender/50 mb-1">One question per group, blank line between groups. First line = question, rest = answer.</label>
                    <textarea data-field="faq_text" rows="6" class="go-input" placeholder="How long does a project take?&#10;Most projects take 2-4 weeks depending on scope.&#10;&#10;Do you offer support after launch?&#10;Yes, every project includes 30 days of free support.">${escHtml(faqToText(data.items))}</textarea>`;
            default:
                return '';
        }
    }

    function statsToText(stats) {
        if (!Array.isArray(stats)) return '';
        return stats.map(s => `${s.value || ''} | ${s.label || ''}`).join('\n');
    }

    function textToStats(text) {
        return (text || '').split('\n').map(l => l.trim()).filter(Boolean).map(line => {
            const [value, label] = line.split('|').map(s => (s || '').trim());
            return { value: value || '', label: label || '' };
        });
    }

    function faqToText(items) {
        if (!Array.isArray(items)) return '';
        return items.map(i => `${i.q || ''}\n${i.a || ''}`).join('\n\n');
    }

    function textToFaq(text) {
        return (text || '').split(/\n\s*\n/).map(group => group.trim()).filter(Boolean).map(group => {
            const lines = group.split('\n');
            const q = lines.shift() || '';
            return { q: q.trim(), a: lines.join('\n').trim() };
        });
    }

    function escAttr(v) {
        return (v || '').toString().replace(/"/g, '&quot;');
    }
    function escHtml(v) {
        return (v || '').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function readRow(row) {
        const type = row.getAttribute('data-type');
        const get = (name) => {
            const f = row.querySelector(`[data-field="${name}"]`);
            return f ? f.value : '';
        };
        switch (type) {
            case 'heading':
                return { type, text: get('text') };
            case 'paragraph':
                return { type, text: get('text') };
            case 'image':
                return { type, url: get('url'), caption: get('caption') };
            case 'quote':
                return { type, text: get('text'), attribution: get('attribution') };
            case 'stat_row':
                return { type, stats: textToStats(get('stats_text')) };
            case 'faq':
                return { type, items: textToFaq(get('faq_text')) };
            default:
                return null;
        }
    }

    const GoSectionsBuilder = {
        init(opts) {
            this.listEl = opts.listEl;
            this.hiddenInputEl = opts.hiddenInputEl;
            this.formEl = opts.formEl;

            (opts.initial || []).forEach(block => this.addRow(block.type, block));

            this.formEl.addEventListener('submit', () => {
                this.hiddenInputEl.value = JSON.stringify(this.serialize());
            });
        },

        addRow(type, data) {
            const row = el('div', 'sections-row border border-lavender/15 rounded-lg p-4 mb-3 bg-white/[0.02]');
            row.setAttribute('data-type', type);
            row.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-sharp-purple">${BLOCK_LABELS[type] || type}</span>
                    <div class="flex gap-2">
                        <button type="button" class="go-row-up text-lavender/40 hover:text-lavender text-xs px-2">&#8593;</button>
                        <button type="button" class="go-row-down text-lavender/40 hover:text-lavender text-xs px-2">&#8595;</button>
                        <button type="button" class="go-row-remove text-red-400/70 hover:text-red-400 text-xs px-2">Remove</button>
                    </div>
                </div>
                <div class="sections-row-fields">${fieldsFor(type, data)}</div>
            `;
            row.querySelector('.go-row-remove').addEventListener('click', () => row.remove());
            row.querySelector('.go-row-up').addEventListener('click', () => {
                const prev = row.previousElementSibling;
                if (prev) row.parentNode.insertBefore(row, prev);
            });
            row.querySelector('.go-row-down').addEventListener('click', () => {
                const next = row.nextElementSibling;
                if (next) row.parentNode.insertBefore(next, row);
            });
            this.listEl.appendChild(row);
        },

        serialize() {
            return Array.from(this.listEl.querySelectorAll('.sections-row'))
                .map(readRow)
                .filter(Boolean);
        },
    };

    window.GoSectionsBuilder = GoSectionsBuilder;
})(window);
