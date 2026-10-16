<section class="card">
  <h1>Edit profile</h1>
  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" action="<?= url('profile/edit') ?>" enctype="multipart/form-data" class="stack">
    <?= csrf_field() ?>
    <div class="photo-row">
      <img class="avatar xl" src="<?= e(avatar($user['profile_image'])) ?>" alt="">
      <label>Profile picture
        <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp,image/gif">
      </label>
    </div>
    <label>Full name
      <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" minlength="2" maxlength="60" required>
    </label>
    <label>Username
      <input type="text" value="<?= e($user['username']) ?>" disabled>
    </label>
    <label>Bio
      <textarea name="bio" rows="4" maxlength="300" placeholder="Tell people a little about you"><?= e($user['bio'] ?? '') ?></textarea>
    </label>
    <div class="row-end"><a class="btn ghost" href="<?= url('profile/show/' . $user['username']) ?>">Cancel</a><button class="btn" type="submit">Save changes</button></div>
  </form>
</section>
