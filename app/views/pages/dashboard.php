<?php if (empty($currentProjectId)): ?>
    <?php include __DIR__ . '/../partials/empty-state.php'; ?>
<?php else: ?>
    <div style="padding: var(--space-lg);">
        <h1><?= htmlspecialchars($currentProjectName) ?> - Dashboard</h1>
        <p>Populated dashboard content goes here.</p>
    </div>
<?php endif; ?>