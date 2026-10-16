<?php
require __DIR__ . '/../config/config.php';
require APP_PATH . '/core/helpers.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

spl_autoload_register(function ($class) {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $f = APP_PATH . "/$dir/$class.php";
        if (is_file($f)) { require $f; return; }
    }
});

if (!APP_DEBUG) ini_set('display_errors', '0');

$parts  = array_values(array_filter(explode('/', trim($_GET['url'] ?? '', '/')), 'strlen'));
$ctrl   = $parts[0] ?? (auth_id() ? 'feed' : 'auth');
$action = $parts[1] ?? ($ctrl === 'auth' ? 'login' : 'index');
$args   = array_slice($parts, 2);

try {
    if (!preg_match('/^[a-z]+$/', $ctrl) || !preg_match('/^[a-z]+$/i', $action)) throw new BadMethodCallException();
    $class = ucfirst($ctrl) . 'Controller';
    if (!is_file(APP_PATH . "/controllers/$class.php")) throw new BadMethodCallException();
    $m = new ReflectionMethod($class, $action);
    if (!$m->isPublic() || $m->class !== $class) throw new BadMethodCallException();
    call_user_func_array([new $class, $action], $args);
} catch (BadMethodCallException | ReflectionException | ArgumentCountError | TypeError $e) {
    http_response_code(404);
    $title = 'Not found'; $me = null; $flashes = [];
    require APP_PATH . '/views/layout/header.php';
    require APP_PATH . '/views/errors/404.php';
    require APP_PATH . '/views/layout/footer.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo APP_DEBUG ? '<pre>' . e($e->getMessage()) . "\n" . e($e->getTraceAsString()) . '</pre>' : 'Something went wrong.';
}
