<?php
class SearchController extends Controller {
    public function index(): void {
        $q = trim($_GET['q'] ?? '');
        $type = ($_GET['type'] ?? 'users') === 'posts' ? 'posts' : 'users';
        $page = $this->pageNumber();
        $users = $posts = []; $prevUrl = $nextUrl = null;

        if ($q !== '') {
            if ($type === 'users') {
                $users = (new UserModel)->search($q);
            } else {
                $rows = (new PostModel)->search((int)$this->me['id'], $q, PER_PAGE + 1, ($page - 1) * PER_PAGE);
                $more = count($rows) > PER_PAGE;
                $posts = $this->withComments(array_slice($rows, 0, PER_PAGE));
                $prevUrl = $page > 1 ? url('search', ['q' => $q, 'type' => 'posts', 'page' => $page - 1]) : null;
                $nextUrl = $more ? url('search', ['q' => $q, 'type' => 'posts', 'page' => $page + 1]) : null;
            }
        }
        $this->view('search/index', compact('q', 'type', 'users', 'posts', 'prevUrl', 'nextUrl') + ['title' => 'Search']);
    }
}
