<?php
$success = Session::getFlash('success');
$error   = Session::getFlash('error');
?>
<?php if ($success): ?>
    <script>window.addEventListener('DOMContentLoaded', () => 
        window.showToast('success', <?= json_encode($success) ?>)
    );</script>
<?php endif; ?>
<?php if ($error): ?>
    <script>window.addEventListener('DOMContentLoaded', () => 
        window.showToast('error', <?= json_encode($error) ?>)
    );</script>
<?php endif; ?>