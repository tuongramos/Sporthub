<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'bootstrap.php';

$router = new Router();

require_once __DIR__ . '/../app/routes/api.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
if ($uri === '') $uri = '/';

$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);
?>
