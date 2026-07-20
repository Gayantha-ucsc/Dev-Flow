<?php
// Expects: 
    // $icon (renderIcon name), 
    // $heading, 
    // $subtext, 
    // $ctaText (optional), 
    // $ctaHref (optional)
?>
<div class="empty-state">
    <div class="empty-state__icon"><?= renderIcon($icon) ?></div>
    <h1 class="empty-state__heading"><?= htmlspecialchars($heading) ?></h1>
    <p class="empty-state__subtext"><?= htmlspecialchars($subtext) ?></p>
    <?php if (!empty($ctaText) && !empty($ctaHref)): ?>
        <a href="<?= url($ctaHref) ?>" class="empty-state__cta">
            <?= renderIcon('plus') ?>
            <span><?= htmlspecialchars($ctaText) ?></span>
        </a>
    <?php endif; ?>
</div>