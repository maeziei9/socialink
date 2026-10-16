<?php
class PostModel extends Model {
    private function base(): string {
        return 'SELECT p.id, p.user_id, p.content, p.image, p.created_at, p.updated_at,
                       u.username, u.full_name, u.profile_image,
                       (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id)  AS comment_count,
                       (SELECT r.type FROM reactions r WHERE r.post_id = p.id AND r.user_id = ?) AS my_reaction
                FROM posts p JOIN users u ON u.id = p.user_id ';
    }
    private const ORDER = 'ORDER BY p.created_at DESC, p.id DESC LIMIT ? OFFSET ?';

    public function feed(int $me, int $limit, int $offset): array {
        return $this->withReactions($this->all($this->base() . self::ORDER, [$me, $limit, $offset]));
    }
    public function byUser(int $me, int $userId, int $limit, int $offset): array {
        return $this->withReactions($this->all($this->base() . 'WHERE p.user_id = ? ' . self::ORDER, [$me, $userId, $limit, $offset]));
    }
    public function search(int $me, string $q, int $limit, int $offset): array {
        return $this->withReactions($this->all($this->base() . 'WHERE p.content LIKE ? ' . self::ORDER, [$me, $this->like($q), $limit, $offset]));
    }
    public function find(int $id): ?array { return $this->one('SELECT * FROM posts WHERE id = ?', [$id]); }
    public function countByUser(int $userId): int {
        return (int)$this->run('SELECT COUNT(*) FROM posts WHERE user_id = ?', [$userId])->fetchColumn();
    }

    public function create(int $userId, string $content, ?string $image): int {
        $this->run('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)', [$userId, $content, $image]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, int $userId, string $content, ?string $image = null): void {
        if ($image !== null) {
            $this->run('UPDATE posts SET content = ?, image = ? WHERE id = ? AND user_id = ?', [$content, $image, $id, $userId]);
        } else {
            $this->run('UPDATE posts SET content = ? WHERE id = ? AND user_id = ?', [$content, $id, $userId]);
        }
    }
    public function removeImage(int $id, int $userId): void {
        $this->run('UPDATE posts SET image = NULL WHERE id = ? AND user_id = ?', [$id, $userId]);
    }
    public function delete(int $id, int $userId): void {
        $this->run('DELETE FROM posts WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    /** Adds ['reactions' => [type => count], 'reaction_total' => n] to each post. */
    private function withReactions(array $posts): array {
        $ids = array_column($posts, 'id');
        $counts = [];
        if ($ids) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            foreach ($this->all("SELECT post_id, type, COUNT(*) AS n FROM reactions WHERE post_id IN ($in) GROUP BY post_id, type", $ids) as $r) {
                $counts[$r['post_id']][$r['type']] = (int)$r['n'];
            }
        }
        foreach ($posts as &$p) {
            $p['reactions'] = $counts[$p['id']] ?? [];
            $p['reaction_total'] = array_sum($p['reactions']);
        }
        return $posts;
    }
}
