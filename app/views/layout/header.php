<?php
$cur = explode('/', trim($_GET['url'] ?? 'feed', '/'))[0] ?: 'feed';
$nav = $nav ?? ['requests' => 0, 'unread' => 0];
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | SOciaLink</title>
  <link rel="icon" href="<?= asset('img/logo-icon.png') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="<?= $me ? 'has-nav' : 'guest' ?>">
<?php if ($me): ?>
<header class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="<?= url('feed') ?>"><img src="<?= asset('img/logo-icon.png') ?>" alt=""><span>SOciaLink</span></a>
    <form class="search" action="<?= base_url() ?>/index.php" method="get" role="search">
      <input type="hidden" name="url" value="search">
      <i class="bi bi-search"></i>
      <input type="search" name="q" placeholder="Search people or posts" aria-label="Search">
    </form>
    <nav class="nav">
      <a href="<?= url('feed') ?>" class="<?= $cur === 'feed' ? 'on' : '' ?>"><i class="bi bi-house"></i><span>Home</span></a>
      <a href="<?= url('friend') ?>" class="<?= $cur === 'friend' ? 'on' : '' ?>"><i class="bi bi-people"></i><span>Friends</span>
        <?php if ($nav['requests']): ?><em class="badge"><?= (int)$nav['requests'] ?></em><?php endif; ?></a>
      <a href="<?= url('message') ?>" class="<?= $cur === 'message' ? 'on' : '' ?>"><i class="bi bi-chat-dots"></i><span>Messages</span>
        <?php if ($nav['unread']): ?><em class="badge"><?= (int)$nav['unread'] ?></em><?php endif; ?></a>
      <a href="<?= url('profile/show/' . $me['username']) ?>" class="<?= $cur === 'profile' ? 'on' : '' ?>"><i class="bi bi-person"></i><span>Profile</span></a>
      <form method="post" action="<?= url('auth/logout') ?>" class="logout">
        <?= csrf_field() ?>
        <button type="submit" title="Log out"><i class="bi bi-box-arrow-right"></i><span>Log out</span></button>
      </form>
    </nav>
  </div>
</header>
<?php endif; ?>
<main class="<?= $me ? 'container' : 'container narrow' ?>">
<?php foreach ($flashes as [$type, $msg]): ?>
  <div class="flash flash-<?= e($type) ?>" role="status"><?= e($msg) ?></div>
<?php endforeach; ?>
