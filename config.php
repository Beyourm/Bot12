<?php

declare(strict_types=1);

if (!defined('BOT_FACTORY_BASE_DIR')) {
    define('BOT_FACTORY_BASE_DIR', __DIR__);
}

if (!function_exists('factory_load_env')) {
    function factory_load_env(): array
    {
        $path = BOT_FACTORY_BASE_DIR . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($path)) {
            return [];
        }

        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $values = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $value = preg_replace('/^(?:["\'])(.*)(?:["\'])$/u', '$1', $value);
            $values[$key] = $value;
        }

        return $values;
    }
}

if (!function_exists('factory_env')) {
    function factory_env(string $key, $default = '')
    {
        static $cache = null;

        if ($cache === null) {
            $cache = factory_load_env();
        }

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $value = getenv($key);
        return $value !== false && $value !== '' ? $value : $default;
    }
}

if (!function_exists('factory_ensure_dir')) {
    function factory_ensure_dir(string $dir): bool
    {
        if ($dir === '' || $dir === '.' || $dir === DIRECTORY_SEPARATOR) {
            return true;
        }

        if (is_dir($dir)) {
            return true;
        }

        $mkdir = @mkdir($dir, 0755, true);
        return $mkdir || is_dir($dir);
    }
}

if (!function_exists('factory_safe_path')) {
    function factory_safe_path(string $base, string $relative): string
    {
        $relative = trim($relative);
        $base = rtrim($base, DIRECTORY_SEPARATOR);

        if ($relative === '') {
            return $base;
        }

        $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $relative);
        return $base . DIRECTORY_SEPARATOR . ltrim($normalized, DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('factory_read_json_file')) {
    function factory_read_json_file(string $path, $default = [])
    {
        if (!is_file($path)) {
            return $default;
        }

        $raw = @file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $default;
    }
}

if (!function_exists('factory_write_json_file')) {
    function factory_write_json_file(string $path, $data, bool $pretty = true): bool
    {
        $dir = dirname($path);
        if ($dir !== '' && !is_dir($dir)) {
            factory_ensure_dir($dir);
        }

        $json = json_encode(
            $data,
            ($pretty ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES : JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if ($json === false) {
            return false;
        }

        return @file_put_contents($path, $json . PHP_EOL, LOCK_EX) !== false;
    }
}

if (!function_exists('factory_write_text_file')) {
    function factory_write_text_file(string $path, string $content): bool
    {
        $dir = dirname($path);
        if ($dir !== '' && !is_dir($dir)) {
            factory_ensure_dir($dir);
        }

        return @file_put_contents($path, $content, LOCK_EX) !== false;
    }
}

if (!function_exists('factory_bot_request')) {
    function factory_bot_request(string $method, array $params = [])
    {
        $token = factory_env('BOT_TOKEN', defined('API_KEY') ? API_KEY : factory_env('TELEGRAM_BOT_TOKEN', ''));

        if ($token === '') {
            return (object) [
                'ok' => false,
                'error_code' => 401,
                'description' => 'Bot token is missing.',
            ];
        }

        $url = 'https://api.telegram.org/bot' . $token . '/' . $method;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_POSTFIELDS => $params,
        ]);

        $body = curl_exec($ch);
        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return (object) [
                'ok' => false,
                'error_code' => 0,
                'description' => $error,
            ];
        }

        curl_close($ch);

        $response = json_decode($body);
        return is_object($response) ? $response : (object) [
            'ok' => false,
            'error_code' => 500,
            'description' => 'Telegram API returned an invalid response.',
        ];
    }
}

if (!function_exists('factory_http_json')) {
    function factory_http_json(string $url, array $options = []): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => $options['method'] ?? 'GET',
                'timeout' => $options['timeout'] ?? 30,
                'ignore_errors' => true,
                'header' => $options['header'] ?? "Content-Type: application/json\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        if (!empty($options['body'])) {
            $context['http']['content'] = is_string($options['body']) ? $options['body'] : json_encode($options['body']);
        }

        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            return ['ok' => false, 'error' => 'Request failed'];
        }

        $decoded = json_decode($result, true);
        return is_array($decoded) ? $decoded : ['ok' => false, 'raw' => $result];
    }
}

if (!function_exists('factory_strip_mention')) {
    function factory_strip_mention(string $value): string
    {
        $value = trim($value);
        $value = ltrim($value, '@');
        return $value;
    }
}

if (!function_exists('factory_is_truthy')) {
    function factory_is_truthy($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value !== 0.0;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            return in_array($normalized, ['1', 'true', 'yes', 'y', 'on'], true);
        }

        return false;
    }
}

if (!function_exists('factory_array_value')) {
    function factory_array_value(array $array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }
}

if (!function_exists('factory_log')) {
    function factory_log(string $message): void
    {
        if (defined('BOT_FACTORY_LOG_PATH')) {
            $logPath = BOT_FACTORY_LOG_PATH;
        } else {
            $logPath = BOT_FACTORY_BASE_DIR . DIRECTORY_SEPARATOR . 'factory.log';
        }

        $timestamp = date('Y-m-d H:i:s');
        $line = '[' . $timestamp . '] ' . $message . PHP_EOL;
        @error_log($line, 3, $logPath);
    }
}

if (!function_exists('factory_safe_json_encode')) {
    function factory_safe_json_encode($data)
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return $json === false ? '[]' : $json;
    }
}

if (!function_exists('factory_is_numeric_id')) {
    function factory_is_numeric_id($value): bool
    {
        return is_numeric((string) $value);
    }
}

if (!function_exists('factory_array_unique_strings')) {
    function factory_array_unique_strings(array $list): array
    {
        $clean = [];
        foreach ($list as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $clean[] = $item;
            }
        }

        return array_values(array_unique($clean));
    }
}

if (!function_exists('factory_normalize_user_list')) {
    function factory_normalize_user_list($data): array
    {
        if (!is_array($data)) {
            return [];
        }

        $out = [];
        foreach ($data as $item) {
            $value = trim((string) $item);
            if ($value !== '') {
                $out[] = $value;
            }
        }

        return array_values(array_unique($out));
    }
}

if (!function_exists('factory_env_bool')) {
    function factory_env_bool(string $key, bool $default = false): bool
    {
        $value = factory_env($key, $default ? '1' : '0');
        return factory_is_truthy($value);
    }
}
