<?php

declare(strict_types=1);

if (!defined('BOT_FACTORY_BASE_DIR')) {
    define('BOT_FACTORY_BASE_DIR', __DIR__);
}

if (!function_exists('factory_ensure_dir')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
}

if (!defined('BOT_FACTORY_PROJECT_ROOT')) {
    define('BOT_FACTORY_PROJECT_ROOT', BOT_FACTORY_BASE_DIR);
}

$required_dirs = [
    'sudo',
    'data',
    'botmak',
    'user',
    'from_id',
    'wataw',
];

foreach ($required_dirs as $dir) {
    factory_ensure_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . $dir);
}

if (!defined('BOT_FACTORY_TZ')) {
    define('BOT_FACTORY_TZ', 'Asia/Baghdad');
}

date_default_timezone_set(BOT_FACTORY_TZ);

if (file_exists(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'rido.php')) {
    require_once BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'rido.php';
}
