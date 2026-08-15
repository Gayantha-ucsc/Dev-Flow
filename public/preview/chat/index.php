<?php
/**
 * Chat System – supports ?view=client or ?view=team-lead for role preview
 */
$assetBase = '../../assets';
$navBase   = '../';
$pageTitle = 'Chat';
$activeNav = 'chat';
$pageStyles = ['canvas.css', 'chat.css'];
$pageScripts = ['chat.js'];

$chatView = isset($_GET['view']) ? strtolower($_GET['view']) : 'team-lead';
$isClientView = ($chatView === 'client');

$currentRole = $isClientView ? 'CLIENT' : 'TEAM LEAD';
$currentUser = $isClientView ? 'Client One' : 'User One';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../partials/canvas/chat-system.php';
include __DIR__ . '/../includes/footer.php';
