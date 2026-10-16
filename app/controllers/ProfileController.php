<?php
class ProfileController extends Controller {
    public function show(string $username = ''): void {
        $users = new UserModel;
        $user = $username === '' ? $this->me : ($users->findByUsername($username) ?? $this->notFound());
        $me = (int)$this->me['id'];
        $page = $this->pageNumber();
        $posts = (new PostModel)->byUser($me, (int)$user['id'], PER_PAGE + 1, ($page - 1) * PER_PAGE);
        $hasMore = count($posts) > PER_PAGE;
        $friends = new FriendModel;
        $this->view('profile/show', [
            'title'      => $user['full_name'],
            'user'       => $user,
            'posts'      => $this->withComments(array_slice($posts, 0, PER_PAGE)),
            'postCount'  => (new PostModel)->countByUser((int)$user['id']),
            'friendCount'=> $friends->friendCount((int)$user['id']),
            'relation'   => $friends->relation($me, (int)$user['id']),
            'prevUrl'    => $page > 1 ? url('profile/show/' . $user['username'], ['page' => $page - 1]) : null,
            'nextUrl'    => $hasMore ? url('profile/show/' . $user['username'], ['page' => $page + 1]) : null,
        ]);
    }

    public function edit(): void {
        $errors = [];
        $user = $this->me;
        if ($this->isPost()) {
            $this->checkCsrf();
            $full = trim($_POST['full_name'] ?? '');
            $bio  = trim($_POST['bio'] ?? '');
            $image = upload_image($_FILES['profile_image'] ?? null, $imgErr);
            if ($imgErr) $errors[] = $imgErr;
            if (mb_strlen($full) < 2 || mb_strlen($full) > 60) $errors[] = 'Enter your full name (2-60 characters).';
            if (mb_strlen($bio) > 300) $errors[] = 'Bio can be up to 300 characters.';
            if (!$errors) {
                (new UserModel)->update((int)$user['id'], $full, $bio === '' ? null : $bio, $image);
                if ($image) delete_upload($user['profile_image']);
                flash('success', 'Profile saved.');
                redirect('profile/show/' . $user['username']);
            }
            delete_upload($image);
            $user['full_name'] = $full; $user['bio'] = $bio;
        }
        $this->view('profile/edit', ['title' => 'Edit profile', 'user' => $user, 'errors' => $errors]);
    }
}
