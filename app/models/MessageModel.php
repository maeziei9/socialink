<?php
class MessageModel extends Model {
    public function send(int $from, int $to, string $body): int {
        $this->run('INSERT INTO messages (sender_id, receiver_id, body) VALUES (?, ?, ?)', [$from, $to, $body]);
        return (int)$this->db->lastInsertId();
    }

    /** Latest 100 messages between two users with id greater than $afterId, oldest first. */
    public function thread(int $a, int $b, int $afterId = 0): array {
        return $this->all('SELECT * FROM (
                             SELECT id, sender_id, body, created_at FROM messages
                             WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND id > ?
                             ORDER BY id DESC LIMIT 100) t ORDER BY id ASC', [$a, $b, $b, $a, $afterId]);
    }
    public function markRead(int $me, int $from): void {
        $this->run('UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0', [$me, $from]);
    }
    public function unreadCount(int $me): int {
        return (int)$this->run('SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0', [$me])->fetchColumn();
    }

    /** One row per conversation: the other user, last message and unread count. */
    public function inbox(int $me): array {
        return $this->all('SELECT u.id, u.username, u.full_name, u.profile_image,
                                  m.body, m.created_at, m.sender_id,
                                  (SELECT COUNT(*) FROM messages x WHERE x.sender_id = u.id AND x.receiver_id = ? AND x.is_read = 0) AS unread
                           FROM messages m
                           JOIN users u ON u.id = IF(m.sender_id = ?, m.receiver_id, m.sender_id)
                           WHERE m.id IN (SELECT MAX(id) FROM messages WHERE sender_id = ? OR receiver_id = ?
                                          GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id))
                           ORDER BY m.id DESC', [$me, $me, $me, $me]);
    }
}
