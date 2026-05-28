<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/app/helpers.php';

$app = require dirname(__DIR__) . '/app/bootstrap.php';
$app->run();
