<h1 class="page-title">Messages</h1>
<section class="card list">
  <?php if (!$conversations): ?>
    <div class="empty inline"><p>No conversations yet. Open a friend's profile and choose Message.</p><a class="btn ghost" href="<?= url('friend') ?>">Go to friends</a></div>
  <?php endif; ?>
  <?php foreach ($conversations as $c): ?>
  <a class="convo<?= $c['unread'] ? ' unread' : '' ?>" href="<?= url('message/thread/' . (int)$c['id']) ?>">
    <img class="avatar" src="<?= e(avatar($c['profile_image'])) ?>" alt="">
    <span class="convo-text">
      <strong><?= e($c['full_name']) ?></strong>
      <small class="muted"><?= (int)$c['sender_id'] !== (int)$c['id'] ? 'You: ' : '' ?><?= e(mb_strimwidth($c['body'], 0, 70, '...')) ?></small>
    </span>
    <span class="convo-meta"><small class="muted"><?= e(time_ago($c['created_at'])) ?></small>
      <?php if ($c['unread']): ?><em class="badge"><?= (int)$c['unread'] ?></em><?php endif; ?></span>
  </a>
  <?php endforeach; ?>
</section>
