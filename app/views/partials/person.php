<div class="person">
  <a class="who" href="<?= url('profile/show/' . $person['username']) ?>">
    <img class="avatar" src="<?= e(avatar($person['profile_image'])) ?>" alt="">
    <span><strong><?= e($person['full_name']) ?></strong><small class="muted">@<?= e($person['username']) ?></small></span>
  </a>
  <div class="person-actions"><?= $actions ?? '' ?></div>
</div>
