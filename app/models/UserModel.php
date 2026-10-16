<?php
class UserModel extends Model {
    private const COLS = 'id, username, full_name, bio, profile_image, created_at';

    public function find(int $id): ?array { return $this->one('SELECT ' . self::COLS . ' FROM users WHERE id = ?', [$id]); }
    public function findByUsername(string $u): ?array { return $this->one('SELECT ' . self::COLS . ' FROM users WHERE username = ?', [$u]); }
    public function findForLogin(string $u): ?array { return $this->one('SELECT * FROM users WHERE username = ?', [$u]); }

    public function create(string $username, string $hash, string $fullName, ?string $image): int {
        $this->run('INSERT INTO users (username, password, full_name, profile_image) VALUES (?, ?, ?, ?)',
            [$username, $hash, $fullName, $image]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $fullName, ?string $bio, ?string $image = null): void {
        if ($image !== null) {
            $this->run('UPDATE users SET full_name = ?, bio = ?, profile_image = ? WHERE id = ?', [$fullName, $bio, $image, $id]);
        } else {
            $this->run('UPDATE users SET full_name = ?, bio = ? WHERE id = ?', [$fullName, $bio, $id]);
        }
    }

    public function search(string $q, int $limit = 30): array {
        $like = $this->like($q);
        return $this->all('SELECT ' . self::COLS . ' FROM users WHERE username LIKE ? OR full_name LIKE ? ORDER BY full_name LIMIT ?',
            [$like, $like, $limit]);
    }
}
