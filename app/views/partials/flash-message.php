<?php
$successMessages = Session::getFlash('success');
$errorMessages   = Session::getFlash('error');
?>
<?php foreach ($successMessages as $msg): ?>
    <script>window.addEventListener('DOMContentLoaded', () =>
        window.showToast('success', <?= json_encode($msg) ?>)
    );</script>
<?php endforeach; ?>
<?php foreach ($errorMessages as $msg): ?>
    <script>window.addEventListener('DOMContentLoaded', () =>
        window.showToast('error', <?= json_encode($msg) ?>)
    );</script>
<?php endforeach; ?>