<?php
/* ---------- output / urls ---------- */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function base_url(): string { return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'); }
function url(string $path = '', array $query = []): string {
    $u = base_url() . '/index.php?url=' . ltrim($path, '/');
    return $query ? $u . '&' . http_build_query($query) : $u;
}
function asset(string $p): string { return base_url() . '/assets/' . ltrim($p, '/'); }
function avatar(?string $f): string { return $f ? base_url() . '/uploads/' . rawurlencode($f) : asset('img/avatar.svg'); }
function upload_url(string $f): string { return base_url() . '/uploads/' . rawurlencode($f); }

/* ---------- navigation ---------- */
function redirect(string $path, array $query = []): void { header('Location: ' . url($path, $query)); exit; }
function redirect_back(string $fallback = 'feed', string $anchor = ''): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $host = parse_url($ref, PHP_URL_HOST);
    if ($ref && $host === parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST)) {
        header('Location: ' . preg_replace('/#.*$/', '', $ref) . $anchor); exit;
    }
    header('Location: ' . url($fallback) . $anchor); exit;
}

/* ---------- session / security ---------- */
function auth_id(): ?int { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function flash(string $type, string $msg): void { $_SESSION['flash'][] = [$type, $msg]; }
function take_flash(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

/* ---------- formatting ---------- */
function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', strtotime($datetime));
}

/* ---------- uploads ---------- */
function upload_image(?array $file, ?string &$error = null): ?string {
    $error = null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) { $error = 'The image could not be uploaded.'; return null; }
    if ($file['size'] > MAX_UPLOAD) { $error = 'Images must be 3 MB or smaller.'; return null; }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || !@getimagesize($file['tmp_name'])) { $error = 'Use a JPG, PNG, WebP or GIF image.'; return null; }
    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . '/' . $name)) { $error = 'The image could not be saved.'; return null; }
    return $name;
}
function delete_upload(?string $name): void {
    if ($name && is_file(UPLOAD_PATH . '/' . basename($name))) @unlink(UPLOAD_PATH . '/' . basename($name));
}
