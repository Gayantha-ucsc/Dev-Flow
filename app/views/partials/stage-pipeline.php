<?php
// Expects:
//   $stages      - ordered array of Stage.status values for one project
//                  ("not_started" | "in_progress" | "pending_completion" | "completed")

?>
<div class="stage-pipeline" role="img" aria-label="Stage progress">
    <?php foreach ($stages as $stageStatus): ?>
        <?php
            $modifier = match ($stageStatus) {
                'completed'          => 'stage-pipeline__segment--done',
                'in_progress',
                'pending_completion' => 'stage-pipeline__segment--current',
                default              => 'stage-pipeline__segment--upcoming',
            };
        ?>
        <span class="stage-pipeline__segment <?= $modifier ?>"></span>
    <?php endforeach; ?>
</div>