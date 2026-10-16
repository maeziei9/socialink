<?php
class PostController extends Controller {
    public function create(): void {
        $this->requirePost();
        $content = trim($_POST['content'] ?? '');
        $image = upload_image($_FILES['image'] ?? null, $imgErr);
        if ($imgErr) { flash('error', $imgErr); redirect('feed'); }
        if ($content === '' || mb_strlen($content) > 2000) {
            delete_upload($image);
            flash('error', 'Write something (up to 2000 characters) to post.');
            redirect('feed');
        }
        (new PostModel)->create((int)$this->me['id'], $content, $image);
        flash('success', 'Posted.');
        redirect('feed');
    }

    public function edit(int $id): void {
        $posts = new PostModel;
        $post = $posts->find($id) ?? $this->notFound();
        if ((int)$post['user_id'] !== (int)$this->me['id']) $this->forbidden();
        $errors = [];

        if ($this->isPost()) {
            $this->checkCsrf();
            $content = trim($_POST['content'] ?? '');
            $new = upload_image($_FILES['image'] ?? null, $imgErr);
            if ($imgErr) $errors[] = $imgErr;
            if ($content === '' || mb_strlen($content) > 2000) $errors[] = 'Write something (up to 2000 characters).';
            if (!$errors) {
                if ($new) { delete_upload($post['image']); $posts->update($id, (int)$this->me['id'], $content, $new); }
                else {
                    $posts->update($id, (int)$this->me['id'], $content);
                    if (!empty($_POST['remove_image']) && $post['image']) { delete_upload($post['image']); $posts->removeImage($id, (int)$this->me['id']); }
                }
                flash('success', 'Post updated.');
                redirect('feed', []) ;
            }
            delete_upload($new);
            $post['content'] = $content;
        }
        $this->view('post/edit', ['title' => 'Edit post', 'post' => $post, 'errors' => $errors]);
    }

    public function delete(int $id): void {
        $this->requirePost();
        $posts = new PostModel;
        $post = $posts->find($id) ?? $this->notFound();
        if ((int)$post['user_id'] !== (int)$this->me['id']) $this->forbidden();
        $posts->delete($id, (int)$this->me['id']);
        delete_upload($post['image']);
        flash('success', 'Post deleted.');
        redirect_back('feed');
    }
}
