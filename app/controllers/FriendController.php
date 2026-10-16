<?php
class FriendController extends Controller {
    public function index(): void {
        $f = new FriendModel; $me = (int)$this->me['id'];
        $this->view('friends/index', [
            'title'    => 'Friends',
            'friends'  => $f->friends($me),
            'incoming' => $f->incoming($me),
            'outgoing' => $f->outgoing($me),
        ]);
    }

    public function add(int $userId): void {
        $this->requirePost();
        $me = (int)$this->me['id'];
        if ($userId === $me || !(new UserModel)->find($userId)) $this->notFound();
        $result = (new FriendModel)->request($me, $userId);
        flash('success', $result === 'accepted' ? 'You are now friends.' : ($result === 'sent' ? 'Friend request sent.' : 'Request already sent.'));
        redirect_back('friend');
    }

    public function accept(int $friendshipId): void {
        $this->requirePost();
        $ok = (new FriendModel)->accept($friendshipId, (int)$this->me['id']);
        flash($ok ? 'success' : 'error', $ok ? 'Friend request accepted.' : 'That request is no longer available.');
        redirect_back('friend');
    }

    /** Unfriend, cancel a sent request, or decline a received one. */
    public function remove(int $userId): void {
        $this->requirePost();
        (new FriendModel)->remove((int)$this->me['id'], $userId);
        redirect_back('friend');
    }
}
