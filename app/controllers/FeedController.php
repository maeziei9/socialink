<?php
class FeedController extends Controller {
    public function index(): void {
        $page  = $this->pageNumber();
        $posts = (new PostModel)->feed((int)$this->me['id'], PER_PAGE + 1, ($page - 1) * PER_PAGE);
        $hasMore = count($posts) > PER_PAGE;
        $posts = $this->withComments(array_slice($posts, 0, PER_PAGE));
        $this->view('feed/index', [
            'title'   => 'Home',
            'posts'   => $posts,
            'prevUrl' => $page > 1 ? url('feed', ['page' => $page - 1]) : null,
            'nextUrl' => $hasMore ? url('feed', ['page' => $page + 1]) : null,
        ]);
    }
}
