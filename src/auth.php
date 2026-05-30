<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function current_user(): ?array
{
    start_session();
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
    ];
}

function require_login(): array
{
    $user = current_user();
    if ($user === null) {
        header('Location: login.php');
        exit;
    }
    return $user;
}

function login_user(array $user): void
{
    start_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
}

function logout_user(): void
{
    start_session();
    $_SESSION = [];
    session_destroy();
}

function register_user(string $username, string $password): array
{
    $username = trim($username);
    if ($username === '' || strlen($username) < 3) {
        return ['ok' => false, 'error' => 'Username must be at least 3 characters.'];
    }
    if (strlen($password) < 6) {
        return ['ok' => false, 'error' => 'Password must be at least 6 characters.'];
    }

    $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'That username is already taken.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);

    return ['ok' => true, 'id' => (int) db()->lastInsertId(), 'username' => $username];
}

function authenticate(string $username, string $password): array
{
    $stmt = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([trim($username)]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    return ['ok' => true, 'id' => (int) $user['id'], 'username' => $user['username']];
}

/** Simple CSRF token helpers for form protection. */
function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(?string $token): bool
{
    start_session();
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
