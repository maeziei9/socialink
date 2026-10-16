<section class="card auth">
  <img class="auth-logo" src="<?= asset('img/logo.png') ?>" alt="SOciaLink">
  <h1>Log in</h1>
  <?php foreach ($errors as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" action="<?= url('auth/login') ?>" class="stack">
    <?= csrf_field() ?>
    <label>Username
      <input type="text" name="username" value="<?= e($old['username'] ?? '') ?>" required autofocus autocomplete="username">
    </label>
    <label>Password
      <input type="password" name="password" required autocomplete="current-password">
    </label>
    <button class="btn block" type="submit">Log in</button>
  </form>
  <p class="muted center">New to SOciaLink? <a href="<?= url('auth/register') ?>">Create an account</a></p>
</section>
