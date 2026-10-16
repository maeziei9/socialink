<h1 class="page-title">Friends</h1>

<?php if ($incoming): ?>
<section class="card">
  <h2>Friend requests <span class="count-pill"><?= count($incoming) ?></span></h2>
  <?php foreach ($incoming as $person):
    ob_start(); ?>
    <form method="post" action="<?= url('friend/accept/' . (int)$person['friendship_id']) ?>"><?= csrf_field() ?><button class="btn teal sm" type="submit">Accept</button></form>
    <form method="post" action="<?= url('friend/remove/' . (int)$person['id']) ?>"><?= csrf_field() ?><button class="btn ghost sm" type="submit">Decline</button></form>
    <?php $actions = ob_get_clean(); include APP_PATH . '/views/partials/person.php'; endforeach; ?>
</section>
<?php endif; ?>

<section class="card">
  <h2>Your friends <span class="count-pill"><?= count($friends) ?></span></h2>
  <?php if (!$friends): ?>
    <div class="empty inline"><p>No friends yet. Search for people and send a request.</p><a class="btn ghost" href="<?= url('search') ?>">Find people</a></div>
  <?php endif; ?>
  <?php foreach ($friends as $person):
    ob_start(); ?>
    <a class="btn purple sm" href="<?= url('message/thread/' . (int)$person['id']) ?>"><i class="bi bi-chat-dots"></i> Message</a>
    <form method="post" action="<?= url('friend/remove/' . (int)$person['id']) ?>" data-confirm="Remove <?= e($person['full_name']) ?> from your friends?">
      <?= csrf_field() ?><button class="btn ghost sm" type="submit">Unfriend</button></form>
    <?php $actions = ob_get_clean(); include APP_PATH . '/views/partials/person.php'; endforeach; ?>
</section>

<?php if ($outgoing): ?>
<section class="card">
  <h2>Sent requests <span class="count-pill"><?= count($outgoing) ?></span></h2>
  <?php foreach ($outgoing as $person):
    ob_start(); ?>
    <form method="post" action="<?= url('friend/remove/' . (int)$person['id']) ?>"><?= csrf_field() ?><button class="btn ghost sm" type="submit">Cancel</button></form>
    <?php $actions = ob_get_clean(); include APP_PATH . '/views/partials/person.php'; endforeach; ?>
</section>
<?php endif; ?>
