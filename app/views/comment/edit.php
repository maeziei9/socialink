<section class="card">
  <h1>Edit comment</h1>
  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" action="<?= url('comment/edit/' . (int)$comment['id']) ?>" class="stack">
    <?= csrf_field() ?>
    <label>Comment
      <textarea name="content" rows="3" maxlength="500" required><?= e($comment['content']) ?></textarea>
    </label>
    <div class="row-end"><a class="btn ghost" href="<?= url('feed') ?>">Cancel</a><button class="btn" type="submit">Save changes</button></div>
  </form>
</section>
