<?php $last = $messages ? (int)end($messages)['id'] : 0; ?>
<section class="card chat">
  <header class="chat-head">
    <a class="icon-btn" href="<?= url('message') ?>" aria-label="Back to messages"><i class="bi bi-arrow-left"></i></a>
    <a class="who" href="<?= url('profile/show/' . $other['username']) ?>">
      <img class="avatar" src="<?= e(avatar($other['profile_image'])) ?>" alt="">
      <span><strong><?= e($other['full_name']) ?></strong><small class="muted">@<?= e($other['username']) ?></small></span>
    </a>
  </header>
  <div class="chat-log" id="chat" data-last="<?= $last ?>" data-poll="<?= e(url('message/poll/' . (int)$other['id'])) ?>">
    <?php if (!$messages): ?><p class="muted center" id="chat-empty">Say hello to <?= e(strtok($other['full_name'], ' ')) ?>.</p><?php endif; ?>
    <?php foreach ($messages as $m): $mine = (int)$m['sender_id'] === (int)$me['id']; ?>
      <div class="msg <?= $mine ? 'mine' : 'theirs' ?>"><p><?= e($m["body"]) ?></p><small><?= e(date('M j, g:i A', strtotime($m['created_at']))) ?></small></div>
    <?php endforeach; ?>
  </div>
  <form class="chat-form" method="post" action="<?= url('message/send/' . (int)$other['id']) ?>" id="chat-form">
    <?= csrf_field() ?>
    <input type="text" name="body" maxlength="1000" placeholder="Write a message" autocomplete="off" required autofocus>
    <button class="btn purple" type="submit"><i class="bi bi-send"></i> Send</button>
  </form>
</section>
