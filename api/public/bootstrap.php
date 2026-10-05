<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

spl_autoload_register(function ($class) {
    $directories = [
        '../app/controllers/',
        '../app/core/',
        '../app/dto/',
        '../app/enum/',
        '../app/mappers/',
        '../app/models/',
        '../app/repositories/',
        '../app/services/'
    ];

    foreach ($directories as $dir) {
        $file = __DIR__ . '/' . $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
?>
