<?php
class AuthController extends Controller {
    protected bool $public = true;

    public function login(): void {
        if (auth_id()) redirect('feed');
        $errors = []; $old = [];
        if ($this->isPost()) {
            $this->checkCsrf();
            $username = trim($_POST['username'] ?? '');
            $old = ['username' => $username];
            $user = (new UserModel)->findForLogin($username);
            if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                redirect('feed');
            }
            $errors[] = 'Wrong username or password.';
        }
        $this->view('auth/login', ['title' => 'Log in', 'errors' => $errors, 'old' => $old]);
    }

    public function register(): void {
        if (auth_id()) redirect('feed');
        $errors = []; $old = [];
        if ($this->isPost()) {
            $this->checkCsrf();
            $username = trim($_POST['username'] ?? '');
            $full     = trim($_POST['full_name'] ?? '');
            $pass     = $_POST['password'] ?? '';
            $old = ['username' => $username, 'full_name' => $full];
            $users = new UserModel;

            if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) $errors[] = 'Username must be 3-20 letters, numbers or underscores.';
            elseif ($users->findByUsername($username)) $errors[] = 'That username is taken.';
            if (mb_strlen($full) < 2 || mb_strlen($full) > 60) $errors[] = 'Enter your full name (2-60 characters).';
            if (strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters.';
            if ($pass !== ($_POST['confirm_password'] ?? '')) $errors[] = 'Passwords do not match.';

            $image = upload_image($_FILES['profile_image'] ?? null, $imgErr);
            if ($imgErr) $errors[] = $imgErr;

            if (!$errors) {
                $id = $users->create($username, password_hash($pass, PASSWORD_DEFAULT), $full, $image);
                session_regenerate_id(true);
                $_SESSION['user_id'] = $id;
                flash('success', 'Welcome to SOciaLink!');
                redirect('feed');
            }
            delete_upload($image);
        }
        $this->view('auth/register', ['title' => 'Create account', 'errors' => $errors, 'old' => $old]);
    }

    public function logout(): void {
        $this->requirePost();
        $_SESSION = [];
        session_destroy();
        header('Location: ' . url('auth/login'));
        exit;
    }
}
