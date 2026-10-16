<?php
class CommentModel extends Model {
    /** @return array<int, array> comments grouped by post id, oldest first */
    public function forPosts(array $postIds): array {
        if (!$postIds) return [];
        $in = implode(',', array_fill(0, count($postIds), '?'));
        $rows = $this->all("SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, c.updated_at,
                                   u.username, u.full_name, u.profile_image
                            FROM comments c JOIN users u ON u.id = c.user_id
                            WHERE c.post_id IN ($in) ORDER BY c.created_at ASC, c.id ASC", $postIds);
        $by = [];
        foreach ($rows as $r) $by[$r['post_id']][] = $r;
        return $by;
    }
    public function find(int $id): ?array { return $this->one('SELECT * FROM comments WHERE id = ?', [$id]); }
    public function create(int $postId, int $userId, string $content): void {
        $this->run('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)', [$postId, $userId, $content]);
    }
    public function update(int $id, int $userId, string $content): void {
        $this->run('UPDATE comments SET content = ? WHERE id = ? AND user_id = ?', [$content, $id, $userId]);
    }
    public function delete(int $id, int $userId): void {
        $this->run('DELETE FROM comments WHERE id = ? AND user_id = ?', [$id, $userId]);
    }
}
