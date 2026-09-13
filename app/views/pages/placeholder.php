<div style="padding: var(--space-lg);">
    <h1><?= htmlspecialchars($heading) ?></h1>
    <p style="color: var(--color-text-muted);">This page is under construction - frontend navigation test.</p>
</div>
<?php if (!empty($clientProjects)): ?>
    <div style="padding: 0 var(--space-lg) var(--space-lg);">
        <?php include __DIR__ . '/../partials/client-projects-widget.php'; ?>
    </div>
<?php endif; ?>