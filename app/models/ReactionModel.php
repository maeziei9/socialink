<?php
class ReactionModel extends Model {
    /** key => [emoji, label] */
    public const TYPES = [
        'like' => ['👍', 'Like'],
        'love' => ['❤️', 'Love'],
        'haha' => ['😂', 'Haha'],
        'wow'  => ['😮', 'Wow'],
        'sad'  => ['😢', 'Sad'],
    ];

    public function mine(int $postId, int $userId): ?string {
        $r = $this->one('SELECT type FROM reactions WHERE post_id = ? AND user_id = ?', [$postId, $userId]);
        return $r['type'] ?? null;
    }
    public function set(int $postId, int $userId, string $type): void {
        $this->run('INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE type = VALUES(type)', [$postId, $userId, $type]);
    }
    public function remove(int $postId, int $userId): void {
        $this->run('DELETE FROM reactions WHERE post_id = ? AND user_id = ?', [$postId, $userId]);
    }
}
