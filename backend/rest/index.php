<?php
// This ensures the built-in server serves static files if they exist
if (php_sapi_name() === 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require "vendor/autoload.php";
require __DIR__ . "/services/ExamService.php";
require __DIR__ . '/routes/ExamRoutes.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::register('examService', 'ExamService');
Flight::start();
