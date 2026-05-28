<?php

declare(strict_types=1);

use App\Core\Application;

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

if (file_exists($root . '/.env')) {
    Dotenv\Dotenv::createImmutable($root)->safeLoad();
}

date_default_timezone_set(
    (require $root . '/config/app.php')['timezone'] ?? 'UTC'
);

return Application::boot($root);
