<?php
abstract class Controller {
    protected bool $public = false;      // guest-accessible controllers override this
    protected ?array $me = null;

    public function __construct() {
        if (!$this->public) {
            if (!auth_id()) redirect('auth/login');
            $this->me = (new UserModel)->find(auth_id());
            if (!$this->me) { session_destroy(); header('Location: ' . url('auth/login')); exit; }
        }
    }

    protected function view(string $name, array $data = []): void {
        $data += ['title' => APP_NAME];
        $data['me'] = $this->me;
        if ($this->me) {
            $data['nav'] = [
                'requests' => (new FriendModel)->pendingCount((int)$this->me['id']),
                'unread'   => (new MessageModel)->unreadCount((int)$this->me['id']),
            ];
        }
        $data['flashes'] = take_flash();
        extract($data, EXTR_SKIP);
        require APP_PATH . '/views/layout/header.php';
        require APP_PATH . "/views/$name.php";
        require APP_PATH . '/views/layout/footer.php';
    }

    protected function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

    protected function checkCsrf(): void {
        $t = $_POST['csrf'] ?? '';
        if (!is_string($t) || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
            http_response_code(419);
            exit('Your session expired. Go back, refresh the page and try again.');
        }
    }
    protected function requirePost(): void {
        if (!$this->isPost()) redirect('feed');
        $this->checkCsrf();
    }
    protected function notFound(): void {
        http_response_code(404);
        $this->view('errors/404', ['title' => 'Not found']);
        exit;
    }
    protected function forbidden(): void {
        flash('error', 'You can only change your own content.');
        redirect_back('feed');
    }
    protected function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    protected function pageNumber(): int { return max(1, (int)($_GET['page'] ?? 1)); }

    /** Adds comments to a list of posts. */
    protected function withComments(array $posts): array {
        $by = (new CommentModel)->forPosts(array_column($posts, 'id'));
        foreach ($posts as &$p) $p['comments'] = $by[$p['id']] ?? [];
        return $posts;
    }
}
