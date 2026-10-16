<?php
class ReactionController extends Controller {
    /** Picks a reaction; tapping your current reaction again removes it. */
    public function react(): void {
        $this->requirePost();
        $postId = (int)($_POST['post_id'] ?? 0);
        $type = $_POST['type'] ?? '';
        if (!isset(ReactionModel::TYPES[$type]) || !(new PostModel)->find($postId)) $this->notFound();

        $reactions = new ReactionModel;
        $uid = (int)$this->me['id'];
        if ($reactions->mine($postId, $uid) === $type) $reactions->remove($postId, $uid);
        else $reactions->set($postId, $uid, $type);
        redirect_back('feed', '#post-' . $postId);
    }
}
