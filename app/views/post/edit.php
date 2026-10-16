<section class="card">
  <h1>Edit post</h1>
  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" action="<?= url('post/edit/' . (int)$post['id']) ?>" enctype="multipart/form-data" class="stack">
    <?= csrf_field() ?>
    <label>Post
      <textarea name="content" rows="5" maxlength="2000" required><?= e($post['content']) ?></textarea>
    </label>
    <?php if ($post['image']): ?>
      <img class="post-img" src="<?= e(upload_url($post['image'])) ?>" alt="Current image">
      <label class="check"><input type="checkbox" name="remove_image" value="1"> Remove this image</label>
    <?php endif; ?>
    <label>Replace image <span class="muted">(optional)</span>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
    </label>
    <div class="row-end"><a class="btn ghost" href="<?= url('feed') ?>">Cancel</a><button class="btn" type="submit">Save changes</button></div>
  </form>
</section>
