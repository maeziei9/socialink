<h1 class="page-title">Search</h1>
<section class="card">
  <form method="get" action="<?= base_url() ?>/index.php" class="search-page">
    <input type="hidden" name="url" value="search">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search" aria-label="Search" required>
    <div class="seg" role="radiogroup">
      <label><input type="radio" name="type" value="users" <?= $type === 'users' ? 'checked' : '' ?>><span>People</span></label>
      <label><input type="radio" name="type" value="posts" <?= $type === 'posts' ? 'checked' : '' ?>><span>Posts</span></label>
    </div>
    <button class="btn" type="submit">Search</button>
  </form>
</section>

<?php if ($q !== '' && $type === 'users'): ?>
<section class="card">
  <h2>People <span class="count-pill"><?= count($users) ?></span></h2>
  <?php if (!$users): ?><div class="empty inline"><p>No one matches "<?= e($q) ?>". Try a different name or username.</p></div><?php endif; ?>
  <?php foreach ($users as $person):
    ob_start(); ?>
    <?php if ((int)$person['id'] !== (int)$me['id']): ?><a class="btn purple sm" href="<?= url('message/thread/' . (int)$person['id']) ?>"><i class="bi bi-chat-dots"></i></a><?php endif; ?>
    <?php $actions = ob_get_clean(); include APP_PATH . '/views/partials/person.php'; endforeach; ?>
</section>
<?php elseif ($q !== '' && $type === 'posts'): ?>
  <?php if (!$posts): ?><section class="card empty"><p>No posts match "<?= e($q) ?>".</p></section><?php endif; ?>
  <?php foreach ($posts as $post) include APP_PATH . '/views/partials/post.php'; ?>
  <?php include APP_PATH . '/views/partials/pagination.php'; ?>
<?php endif; ?>
