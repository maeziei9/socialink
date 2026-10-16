<?php
/** Base model: every query goes through prepared statements. */
abstract class Model {
    protected PDO $db;
    public function __construct() { $this->db = Database::get(); }

    protected function run(string $sql, array $params = []): PDOStatement {
        $st = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $v) {
            $st->bindValue($i++, $v, is_int($v) ? PDO::PARAM_INT : (is_null($v) ? PDO::PARAM_NULL : PDO::PARAM_STR));
        }
        $st->execute();
        return $st;
    }
    protected function all(string $sql, array $p = []): array { return $this->run($sql, $p)->fetchAll(); }
    protected function one(string $sql, array $p = []): ?array { $r = $this->run($sql, $p)->fetch(); return $r ?: null; }
    protected function like(string $q): string { return '%' . addcslashes($q, '%_\\') . '%'; }
}
