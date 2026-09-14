<?php
/**
 * Shared "previous / next project" nav strip shown near the bottom of
 * each case-study page under /work/.
 *
 * Required variables a page sets before including this file:
 *   $go_prev = ['href' => '/work/foo', 'label' => 'Foo']
 *   $go_next = ['href' => '/work/bar', 'label' => 'Bar']
 */
?>
<nav class="grid grid-cols-1 md:grid-cols-2 border-t border-lavender/20">
    <a href="<?php echo htmlspecialchars($go_prev['href']); ?>" class="group border-b md:border-b-0 md:border-r border-lavender/20 p-12 md:p-20 flex flex-col items-start hover:bg-card-dark transition-colors hover-target">
        <span class="font-mono text-xs text-lavender/40 mb-4">PREVIOUS PROJECT</span>
        <span class="font-syne text-3xl md:text-5xl group-hover:translate-x-4 transition-transform duration-300"><?php echo htmlspecialchars($go_prev['label']); ?></span>
    </a>
    <a href="<?php echo htmlspecialchars($go_next['href']); ?>" class="group p-12 md:p-20 flex flex-col items-end text-right hover:bg-card-dark transition-colors hover-target">
        <span class="font-mono text-xs text-lavender/40 mb-4">NEXT PROJECT</span>
        <span class="font-syne text-3xl md:text-5xl group-hover:-translate-x-4 transition-transform duration-300"><?php echo htmlspecialchars($go_next['label']); ?></span>
    </a>
</nav>
