<?php
class MessageController extends Controller {
    public function index(): void {
        $this->view('messages/index', ['title' => 'Messages', 'conversations' => (new MessageModel)->inbox((int)$this->me['id'])]);
    }

    public function thread(int $userId): void {
        $me = (int)$this->me['id'];
        $other = (new UserModel)->find($userId);
        if (!$other || $userId === $me) $this->notFound();
        $m = new MessageModel;
        $m->markRead($me, $userId);
        $this->view('messages/thread', [
            'title'    => $other['full_name'],
            'other'    => $other,
            'messages' => $m->thread($me, $userId),
        ]);
    }

    public function send(int $userId): void {
        $this->requirePost();
        $me = (int)$this->me['id'];
        $ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
        $body = trim($_POST['body'] ?? '');
        if ($userId === $me || !(new UserModel)->find($userId)) $this->notFound();

        if ($body === '' || mb_strlen($body) > 1000) {
            if ($ajax) $this->json(['error' => 'Messages can be 1-1000 characters.'], 422);
            flash('error', 'Messages can be 1-1000 characters.');
        } else {
            $m = new MessageModel;
            $id = $m->send($me, $userId, $body);
            if ($ajax) $this->json(['messages' => $this->format($m->thread($me, $userId, $id - 1), $me)]);
        }
        redirect('message/thread/' . $userId);
    }

    /** JSON endpoint used by the chat page to fetch new messages. */
    public function poll(int $userId): void {
        $me = (int)$this->me['id'];
        $m = new MessageModel;
        $rows = $m->thread($me, $userId, (int)($_GET['after'] ?? 0));
        if ($rows) $m->markRead($me, $userId);
        $this->json(['messages' => $this->format($rows, $me)]);
    }

    private function format(array $rows, int $me): array {
        return array_map(fn($r) => [
            'id' => (int)$r['id'], 'mine' => (int)$r['sender_id'] === $me,
            'body' => $r['body'], 'time' => date('M j, g:i A', strtotime($r['created_at'])),
        ], $rows);
    }
}
