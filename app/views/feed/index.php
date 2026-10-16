<section class="card composer">
  <form method="post" action="<?= url('post/create') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="composer-row">
      <img class="avatar" src="<?= e(avatar($me['profile_image'])) ?>" alt="">
      <textarea name="content" rows="2" maxlength="2000" placeholder="What's on your mind, <?= e(strtok($me['full_name'], ' ')) ?>?" required></textarea>
    </div>
    <div class="composer-bar">
      <label class="file-pick" title="Add a photo"><i class="bi bi-image"></i> <span data-file-name>Photo</span>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
      </label>
      <button class="btn" type="submit">Post</button>
    </div>
  </form>
</section>

<?php if (!$posts): ?>
  <section class="card empty"><h2>Nothing here yet</h2><p>Share the first post, or find people to follow along with.</p>
    <a class="btn ghost" href="<?= url('search') ?>">Find people</a></section>
<?php endif; ?>
<?php foreach ($posts as $post) include APP_PATH . '/views/partials/post.php'; ?>
<?php include APP_PATH . '/views/partials/pagination.php'; ?>
