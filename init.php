<?php

declare(strict_types=1);

if (!defined('BOT_FACTORY_BASE_DIR')) {
    define('BOT_FACTORY_BASE_DIR', __DIR__);
}

require_once BOT_FACTORY_BASE_DIR . DIRECTORY_SEPARATOR . 'functions.php';

$__factory_env = factory_load_env();

$token = factory_env('BOT_TOKEN', $__factory_env['BOT_TOKEN'] ?? '1854714316:AAEfxhA0M7khDwRjImRNF8qacOGqDherguM');
$bot_id = factory_env('BOT_ID', $__factory_env['BOT_ID'] ?? '1854714316');
$user_bot_sudo = factory_env('USER_BOT_SUDO', $__factory_env['USER_BOT_SUDO'] ?? 'WWJBot');
$admin_id = factory_env('ADMIN_ID', $__factory_env['ADMIN_ID'] ?? '873158772');
$dev_user = factory_env('DEV_USER', $__factory_env['DEV_USER'] ?? 'T0T010');
$dev_channel = factory_env('DEV_CHANNEL', $__factory_env['DEV_CHANNEL'] ?? 'https://t.me/beyoue');
$folder = factory_env('APP_URL', $__factory_env['APP_URL'] ?? $__factory_env['FOLDER'] ?? 'https://bot-production-8d7c1.up.railway.app');

if ($folder === '' || preg_match('#^https?://#i', $folder) !== 1) {
    $folder = 'https://bot-production-8d7c1.up.railway.app';
}

$dev_id = (string) $admin_id;
$sudo = array((string) $admin_id);
$ameed = (string) $admin_id;

if (!defined('API_KEY')) {
    define('API_KEY', $token);
}

if (!defined('BOT_TOKEN')) {
    define('BOT_TOKEN', $token);
}

if (!defined('BOT_FACTORY_ADMIN_ID')) {
    define('BOT_FACTORY_ADMIN_ID', (string) $admin_id);
}

if (!defined('BOT_FACTORY_BASE_URL')) {
    define('BOT_FACTORY_BASE_URL', $folder);
}

if (!defined('BOT_FACTORY_DEV_USER')) {
    define('BOT_FACTORY_DEV_USER', $dev_user);
}

if (!defined('BOT_FACTORY_DEV_CHANNEL')) {
    define('BOT_FACTORY_DEV_CHANNEL', $dev_channel);
}

if (!defined('BOT_FACTORY_USER_BOT_SUDO')) {
    define('BOT_FACTORY_USER_BOT_SUDO', $user_bot_sudo);
}

if (!defined('BOT_FACTORY_BOT_ID')) {
    define('BOT_FACTORY_BOT_ID', (string) $bot_id);
}

unset($__factory_env);

// Backward-compatible variables expected by the existing project.
// These are intentionally preserved to avoid breaking current bot templates.
$token = (string) $token;
$bot_id = (string) $bot_id;
$user_bot_sudo = (string) $user_bot_sudo;
$admin_id = (string) $admin_id;
$dev_user = (string) $dev_user;
$dev_channel = (string) $dev_channel;
$folder = (string) $folder;
$dev_id = (string) $dev_id;
$sudo = array_values(array_unique($sudo));
$ameed = (string) $ameed;
