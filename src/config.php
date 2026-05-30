<?php
/**
 * Loads configuration from environment variables, with a fallback to a local
 * .env file (simple KEY=VALUE parser) so the app runs both in Docker and on a
 * traditional PHP host where env vars are set via the control panel.
 */

declare(strict_types=1);

(function (): void {
    $envFile = __DIR__ . '/../.env';
    if (is_readable($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if ($key !== '' && getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
})();

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        return $value === false ? $default : $value;
    }
}

return [
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'inventory'),
        'user' => env('DB_USER', 'inventory'),
        'pass' => env('DB_PASS', 'inventory'),
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => env('APP_NAME', 'Inventory Manager'),
    ],
];
