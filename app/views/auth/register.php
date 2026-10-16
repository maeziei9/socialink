<section class="card auth">
  <img class="auth-logo" src="<?= asset('img/logo.png') ?>" alt="SOciaLink">
  <h1>Create your account</h1>
  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" action="<?= url('auth/register') ?>" enctype="multipart/form-data" class="stack">
    <?= csrf_field() ?>
    <label>Full name
      <input type="text" name="full_name" value="<?= e($old['full_name'] ?? '') ?>" minlength="2" maxlength="60" required autocomplete="name">
    </label>
    <label>Username
      <input type="text" name="username" value="<?= e($old['username'] ?? '') ?>" pattern="[A-Za-z0-9_]{3,20}" maxlength="20" required autocomplete="username"
             title="3-20 letters, numbers or underscores">
      <small class="muted">3-20 letters, numbers or underscores</small>
    </label>
    <label>Password
      <input type="password" name="password" id="pw" minlength="8" required autocomplete="new-password">
      <small class="muted">At least 8 characters</small>
    </label>
    <label>Confirm password
      <input type="password" name="confirm_password" data-match="#pw" required autocomplete="new-password">
    </label>
    <label>Profile picture <span class="muted">(optional)</span>
      <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp,image/gif">
    </label>
    <button class="btn block" type="submit">Create account</button>
  </form>
  <p class="muted center">Already have an account? <a href="<?= url('auth/login') ?>">Log in</a></p>
</section>
