<?php
class FriendModel extends Model {
    private const USER = 'u.id, u.username, u.full_name, u.bio, u.profile_image';

    public function between(int $a, int $b): ?array {
        return $this->one('SELECT * FROM friendships
                           WHERE (requester_id = ? AND addressee_id = ?) OR (requester_id = ? AND addressee_id = ?)',
            [$a, $b, $b, $a]);
    }

    /** @return array{state:string,id:?int} state: self|none|friends|sent|received */
    public function relation(int $me, int $other): array {
        if ($me === $other) return ['state' => 'self', 'id' => null];
        $f = $this->between($me, $other);
        if (!$f) return ['state' => 'none', 'id' => null];
        if ($f['status'] === 'accepted') return ['state' => 'friends', 'id' => (int)$f['id']];
        return ['state' => ((int)$f['requester_id'] === $me ? 'sent' : 'received'), 'id' => (int)$f['id']];
    }

    /** Sends a request; if the other person already asked us, accepts instead. */
    public function request(int $me, int $other): string {
        $f = $this->between($me, $other);
        if (!$f) {
            $this->run('INSERT INTO friendships (requester_id, addressee_id) VALUES (?, ?)', [$me, $other]);
            return 'sent';
        }
        if ($f['status'] === 'pending' && (int)$f['addressee_id'] === $me) {
            $this->accept((int)$f['id'], $me);
            return 'accepted';
        }
        return 'exists';
    }
    public function accept(int $id, int $me): bool {
        return $this->run("UPDATE friendships SET status = 'accepted' WHERE id = ? AND addressee_id = ? AND status = 'pending'", [$id, $me])->rowCount() > 0;
    }
    /** Unfriend, cancel a sent request, or decline a received one. */
    public function remove(int $me, int $other): void {
        $this->run('DELETE FROM friendships WHERE (requester_id = ? AND addressee_id = ?) OR (requester_id = ? AND addressee_id = ?)',
            [$me, $other, $other, $me]);
    }

    public function friends(int $me): array {
        return $this->all('SELECT ' . self::USER . " FROM friendships f
                           JOIN users u ON u.id = IF(f.requester_id = ?, f.addressee_id, f.requester_id)
                           WHERE f.status = 'accepted' AND (f.requester_id = ? OR f.addressee_id = ?)
                           ORDER BY u.full_name", [$me, $me, $me]);
    }
    public function incoming(int $me): array {
        return $this->all('SELECT f.id AS friendship_id, ' . self::USER . " FROM friendships f
                           JOIN users u ON u.id = f.requester_id
                           WHERE f.addressee_id = ? AND f.status = 'pending' ORDER BY f.created_at DESC", [$me]);
    }
    public function outgoing(int $me): array {
        return $this->all('SELECT ' . self::USER . " FROM friendships f
                           JOIN users u ON u.id = f.addressee_id
                           WHERE f.requester_id = ? AND f.status = 'pending' ORDER BY f.created_at DESC", [$me]);
    }
    public function pendingCount(int $me): int {
        return (int)$this->run("SELECT COUNT(*) FROM friendships WHERE addressee_id = ? AND status = 'pending'", [$me])->fetchColumn();
    }
    public function friendCount(int $me): int {
        return (int)$this->run("SELECT COUNT(*) FROM friendships WHERE status = 'accepted' AND (requester_id = ? OR addressee_id = ?)", [$me, $me])->fetchColumn();
    }
}
