<?php $mine = (int)$post['user_id'] === (int)$me['id']; ?>
<article class="card post" id="post-<?= (int)$post['id'] ?>">
  <header class="post-head">
    <a href="<?= url('profile/show/' . $post['username']) ?>" class="who">
      <img class="avatar" src="<?= e(avatar($post['profile_image'])) ?>" alt="">
      <span><strong><?= e($post['full_name']) ?></strong>
        <small class="muted">@<?= e($post['username']) ?> &middot; <?= e(time_ago($post['created_at'])) ?><?= $post['updated_at'] ? ' &middot; edited' : '' ?></small></span>
    </a>
    <?php if ($mine): ?>
    <details class="menu">
      <summary aria-label="Post options"><i class="bi bi-three-dots"></i></summary>
      <div class="menu-list">
        <a href="<?= url('post/edit/' . (int)$post['id']) ?>"><i class="bi bi-pencil"></i> Edit</a>
        <form method="post" action="<?= url('post/delete/' . (int)$post['id']) ?>" data-confirm="Delete this post?">
          <?= csrf_field() ?><button type="submit"><i class="bi bi-trash"></i> Delete</button>
        </form>
      </div>
    </details>
    <?php endif; ?>
  </header>

  <p class="post-body"><?= nl2br(e($post['content'])) ?></p>
  <?php if ($post['image']): ?><img class="post-img" src="<?= e(upload_url($post['image'])) ?>" alt="Post image" loading="lazy"><?php endif; ?>

  <form class="reactions" method="post" action="<?= url('reaction/react') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
    <?php foreach (ReactionModel::TYPES as $key => [$emoji, $label]): $n = $post['reactions'][$key] ?? 0; ?>
      <button type="submit" name="type" value="<?= e($key) ?>" class="rx<?= $post['my_reaction'] === $key ? ' on' : '' ?>"
              title="<?= e($label) ?>" aria-label="<?= e($label) ?>" aria-pressed="<?= $post['my_reaction'] === $key ? 'true' : 'false' ?>">
        <span><?= $emoji ?></span><?php if ($n): ?><b><?= (int)$n ?></b><?php endif; ?>
      </button>
    <?php endforeach; ?>
    <span class="grow"></span>
    <span class="muted count"><i class="bi bi-chat"></i> <?= (int)$post['comment_count'] ?></span>
  </form>

  <div class="comments">
    <?php foreach ($post['comments'] as $c): ?>
    <div class="comment" id="comment-<?= (int)$c['id'] ?>">
      <a href="<?= url('profile/show/' . $c['username']) ?>"><img class="avatar sm" src="<?= e(avatar($c['profile_image'])) ?>" alt=""></a>
      <div class="bubble">
        <a class="name" href="<?= url('profile/show/' . $c['username']) ?>"><?= e($c['full_name']) ?></a>
        <small class="muted"><?= e(time_ago($c['created_at'])) ?><?= $c['updated_at'] ? ' &middot; edited' : '' ?></small>
        <p><?= nl2br(e($c['content'])) ?></p>
        <?php if ((int)$c['user_id'] === (int)$me['id']): ?>
        <div class="mini-actions">
          <a href="<?= url('comment/edit/' . (int)$c['id']) ?>">Edit</a>
          <form method="post" action="<?= url('comment/delete/' . (int)$c['id']) ?>" data-confirm="Delete this comment?">
            <?= csrf_field() ?><button type="submit">Delete</button>
          </form>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <form class="comment-form" method="post" action="<?= url('comment/create') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
      <input type="text" name="content" maxlength="500" placeholder="Write a comment" aria-label="Write a comment" required>
      <button class="icon-btn" type="submit" aria-label="Post comment"><i class="bi bi-send"></i></button>
    </form>
  </div>
</article>
