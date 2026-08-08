<?php
// Expects:
//   $icon (renderIcon name),
//   $heading,
//   $subtext,
//   $ctaText (optional), $ctaHref or $ctaTrigger (optional)
//   $variant (optional)

$variant = $variant ?? 'page';
?>
<div class="empty-state empty-state--<?= htmlspecialchars($variant) ?>">
    <div class="empty-state__icon"><?= renderIcon($icon) ?></div>
    <h1 class="empty-state__heading"><?= htmlspecialchars($heading) ?></h1>
    <p class="empty-state__subtext"><?= htmlspecialchars($subtext) ?></p>
    <?php if (!empty($ctaTrigger)): ?>
        <button type="button" class="empty-state__cta" data-modal-trigger="<?= htmlspecialchars($ctaTrigger) ?>">
            <?= renderIcon('plus') ?>
            <span><?= htmlspecialchars($ctaText) ?></span>
        </button>
    <?php elseif (!empty($ctaText) && !empty($ctaHref)): ?>
        <a href="<?= url($ctaHref) ?>" class="empty-state__cta">
            <?= renderIcon('plus') ?>
            <span><?= htmlspecialchars($ctaText) ?></span>
        </a>
    <?php endif; ?>
</div>