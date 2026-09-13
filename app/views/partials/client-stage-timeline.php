<?php
// Expects: $stages - ordered array of ['name' => ..., 'status' => 'completed'|'in_progress'|'not_started', ...]
?>
<div class="client-timeline" role="img" aria-label="Project stage progress">
    <?php foreach ($stages as $stage): ?>
        <?php
            $isDone    = $stage['status'] === 'completed';
            $isCurrent = in_array($stage['status'], ['in_progress', 'pending_completion'], true);
            $nodeClass = $isDone ? 'client-timeline__node--done' : ($isCurrent ? 'client-timeline__node--current' : 'client-timeline__node--upcoming');
        ?>
        <div class="client-timeline__stage">
            <span class="client-timeline__node <?= $nodeClass ?>">
                <?= renderIcon($isDone ? 'check' : clientStageIcon($stage['name'])) ?>
            </span>
            <span class="client-timeline__label <?= $isCurrent ? 'client-timeline__label--current' : '' ?>">
                <?= htmlspecialchars($stage['name']) ?>
            </span>
        </div>
    <?php endforeach; ?>
</div>