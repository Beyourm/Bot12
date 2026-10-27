<?php

declare(strict_types=1);

if (!defined('BOT_FACTORY_BASE_DIR')) {
    define('BOT_FACTORY_BASE_DIR', __DIR__);
}

if (!function_exists('factory_ensure_dir')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

if (!defined('BOT_FACTORY_PROJECT_ROOT')) {
    define('BOT_FACTORY_PROJECT_ROOT', BOT_FACTORY_BASE_DIR);
}

if (!is_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'sudo')) {
    @mkdir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'sudo', 0755, true);
}

if (!is_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'data')) {
    @mkdir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'data', 0755, true);
}

if (!is_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'botmak')) {
    @mkdir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'botmak', 0755, true);
}

if (!is_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'user')) {
    @mkdir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'user', 0755, true);
}

if (!is_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'from_id')) {
    @mkdir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'from_id', 0755, true);
}

if (!is_dir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'wataw')) {
    @mkdir(BOT_FACTORY_PROJECT_ROOT . DIRECTORY_SEPARATOR . 'wataw', 0755, true);
}

if (!defined('BOT_FACTORY_TZ')) {
    define('BOT_FACTORY_TZ', 'Asia/Baghdad');
}

date_default_timezone_set(BOT_FACTORY_TZ);
