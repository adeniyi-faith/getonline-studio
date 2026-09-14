<?php
/**
 * Renders the flexible content blocks used by Portfolio Projects and
 * Site Pages, built in the admin at /wp/admin/portfolio.php and
 * /wp/admin/pages.php. Each block is one associative array with a
 * "type" key; unknown/malformed blocks are skipped rather than
 * breaking the page.
 *
 * Supported types: heading, paragraph, image, quote, stat_row, faq
 *
 * Accent color: dynamic content lets the admin pick any hex color, so
 * accented elements use the Tailwind arbitrary-value syntax
 * `text-[var(--accent)]`, reading a `--accent` CSS custom property set
 * once on a wrapping element (see portfolio-view.php / page-view.php).
 *
 * Usage: go_render_sections($sections);
 */
function go_render_sections($sections) {
    if (!is_array($sections)) {
        return;
    }

    foreach ($sections as $section) {
        if (!is_array($section) || empty($section['type'])) {
            continue;
        }

        switch ($section['type']) {
            case 'heading':
                if (empty($section['text'])) break;
                echo '<h2 class="font-syne text-3xl md:text-5xl font-bold text-lavender mt-16 mb-6 reveal-up">'
                    . htmlspecialchars($section['text']) . '</h2>';
                break;

            case 'paragraph':
                if (empty($section['text'])) break;
                echo '<p class="font-manrope text-lavender/70 text-lg leading-relaxed mb-6 max-w-3xl reveal-up">'
                    . nl2br(htmlspecialchars($section['text'])) . '</p>';
                break;

            case 'image':
                if (empty($section['url'])) break;
                echo '<figure class="my-10 reveal-up">';
                echo '<img src="' . htmlspecialchars($section['url']) . '" alt="'
                    . htmlspecialchars($section['caption'] ?? '') . '" class="w-full rounded-xl border border-lavender/10">';
                if (!empty($section['caption'])) {
                    echo '<figcaption class="mt-3 text-sm text-lavender/40 font-manrope">'
                        . htmlspecialchars($section['caption']) . '</figcaption>';
                }
                echo '</figure>';
                break;

            case 'quote':
                if (empty($section['text'])) break;
                echo '<blockquote class="border-l-2 border-[var(--accent)] pl-6 my-10 reveal-up">';
                echo '<p class="font-syne text-2xl md:text-3xl text-lavender leading-snug">&ldquo;'
                    . htmlspecialchars($section['text']) . '&rdquo;</p>';
                if (!empty($section['attribution'])) {
                    echo '<cite class="block mt-4 text-sm text-lavender/50 not-italic">'
                        . htmlspecialchars($section['attribution']) . '</cite>';
                }
                echo '</blockquote>';
                break;

            case 'stat_row':
                if (empty($section['stats']) || !is_array($section['stats'])) break;
                echo '<div class="flex flex-wrap gap-10 border-t border-lavender/10 pt-8 my-10 reveal-up">';
                foreach ($section['stats'] as $stat) {
                    if (empty($stat['value'])) continue;
                    echo '<div>';
                    echo '<span class="block font-syne text-3xl md:text-4xl text-[var(--accent)]">'
                        . htmlspecialchars($stat['value']) . '</span>';
                    echo '<span class="text-xs text-lavender/50 uppercase tracking-widest">'
                        . htmlspecialchars($stat['label'] ?? '') . '</span>';
                    echo '</div>';
                }
                echo '</div>';
                break;

            case 'faq':
                if (empty($section['items']) || !is_array($section['items'])) break;
                echo '<div class="divide-y divide-lavender/10 border-t border-b border-lavender/10 my-10 reveal-up">';
                foreach ($section['items'] as $item) {
                    if (empty($item['q'])) continue;
                    echo '<div class="py-6">';
                    echo '<h3 class="font-syne text-lg md:text-xl text-lavender mb-2">' . htmlspecialchars($item['q']) . '</h3>';
                    echo '<p class="font-manrope text-lavender/60 leading-relaxed">' . nl2br(htmlspecialchars($item['a'] ?? '')) . '</p>';
                    echo '</div>';
                }
                echo '</div>';
                break;
        }
    }
}
