<?php

declare(strict_types=1);

use App\Core\Application;

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

foreach (['.env', 'pulseos.env'] as $envFile) {
    $envPath = $root . DIRECTORY_SEPARATOR . $envFile;
    if (!is_file($envPath)) {
        continue;
    }
    Dotenv\Dotenv::createImmutable($root, $envFile)->safeLoad();
    break;
}

date_default_timezone_set(
    (require $root . '/config/app.php')['timezone'] ?? 'UTC'
);

return Application::boot($root);
