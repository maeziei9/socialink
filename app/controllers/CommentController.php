<?php
class CommentController extends Controller {
    public function create(): void {
        $this->requirePost();
        $postId = (int)($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        if (!(new PostModel)->find($postId)) $this->notFound();
        if ($content === '' || mb_strlen($content) > 500) {
            flash('error', 'Comments can be 1-500 characters.');
        } else {
            (new CommentModel)->create($postId, (int)$this->me['id'], $content);
        }
        redirect_back('feed', '#post-' . $postId);
    }

    public function edit(int $id): void {
        $comments = new CommentModel;
        $comment = $comments->find($id) ?? $this->notFound();
        if ((int)$comment['user_id'] !== (int)$this->me['id']) $this->forbidden();
        $errors = [];
        if ($this->isPost()) {
            $this->checkCsrf();
            $content = trim($_POST['content'] ?? '');
            if ($content === '' || mb_strlen($content) > 500) $errors[] = 'Comments can be 1-500 characters.';
            else {
                $comments->update($id, (int)$this->me['id'], $content);
                flash('success', 'Comment updated.');
                redirect('feed');
            }
            $comment['content'] = $content;
        }
        $this->view('comment/edit', ['title' => 'Edit comment', 'comment' => $comment, 'errors' => $errors]);
    }

    public function delete(int $id): void {
        $this->requirePost();
        $comments = new CommentModel;
        $comment = $comments->find($id) ?? $this->notFound();
        if ((int)$comment['user_id'] !== (int)$this->me['id']) $this->forbidden();
        $comments->delete($id, (int)$this->me['id']);
        redirect_back('feed', '#post-' . (int)$comment['post_id']);
    }
}
