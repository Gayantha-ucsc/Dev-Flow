<?php
// Fallback "current logged-in user" used when previewing pages without a real
// authenticated session (see currentUserContext() in app/core/helpers.php).
// This is an ordinary demo account, not the administrator, so preview/mock
// browsing reflects a normal user's experience rather than admin access.

return [
    'user_id'         => 2,
    'name'            => 'Demo User',
    'email'           => 'demo@example.com',
    'profile_picture' => null,
    'is_admin'        => false,
];