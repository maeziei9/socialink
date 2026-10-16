<?php $isMe = $relation['state'] === 'self'; $uid = (int)$user['id']; ?>
<section class="card profile">
  <img class="avatar xl" src="<?= e(avatar($user['profile_image'])) ?>" alt="">
  <div class="profile-info">
    <h1><?= e($user['full_name']) ?></h1>
    <p class="muted">@<?= e($user['username']) ?> &middot; Joined <?= e(date('F Y', strtotime($user['created_at']))) ?></p>
    <p class="bio"><?= $user['bio'] ? nl2br(e($user['bio'])) : '<span class="muted">No bio yet.</span>' ?></p>
    <p class="stats"><strong><?= (int)$postCount ?></strong> posts <span class="dot"></span> <strong><?= (int)$friendCount ?></strong> friends</p>
    <div class="profile-actions">
    <?php if ($isMe): ?>
      <a class="btn ghost" href="<?= url('profile/edit') ?>"><i class="bi bi-pencil"></i> Edit profile</a>
    <?php else: ?>
      <?php if ($relation['state'] === 'none'): ?>
        <form method="post" action="<?= url('friend/add/' . $uid) ?>"><?= csrf_field() ?><button class="btn" type="submit"><i class="bi bi-person-plus"></i> Add friend</button></form>
      <?php elseif ($relation['state'] === 'sent'): ?>
        <form method="post" action="<?= url('friend/remove/' . $uid) ?>"><?= csrf_field() ?><button class="btn ghost" type="submit">Cancel request</button></form>
      <?php elseif ($relation['state'] === 'received'): ?>
        <form method="post" action="<?= url('friend/accept/' . (int)$relation['id']) ?>"><?= csrf_field() ?><button class="btn teal" type="submit">Accept request</button></form>
        <form method="post" action="<?= url('friend/remove/' . $uid) ?>"><?= csrf_field() ?><button class="btn ghost" type="submit">Decline</button></form>
      <?php else: ?>
        <form method="post" action="<?= url('friend/remove/' . $uid) ?>" data-confirm="Remove <?= e($user['full_name']) ?> from your friends?">
          <?= csrf_field() ?><button class="btn ghost" type="submit"><i class="bi bi-check2"></i> Friends</button></form>
      <?php endif; ?>
      <a class="btn purple" href="<?= url('message/thread/' . $uid) ?>"><i class="bi bi-chat-dots"></i> Message</a>
    <?php endif; ?>
    </div>
  </div>
</section>

<h2 class="section-title">Posts</h2>
<?php if (!$posts): ?>
  <section class="card empty"><p><?= $isMe ? "You haven't posted yet." : 'No posts yet.' ?></p></section>
<?php endif; ?>
<?php foreach ($posts as $post) include APP_PATH . '/views/partials/post.php'; ?>
<?php include APP_PATH . '/views/partials/pagination.php'; ?>
