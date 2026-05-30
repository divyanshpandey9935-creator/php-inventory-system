<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function render_header(string $title, ?array $user = null): void
{
    $config = require __DIR__ . '/config.php';
    $appName = $config['app']['name'];
    ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> &middot; <?= e($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="dashboard.php">
            <span class="me-1">&#128230;</span><?= e($appName) ?>
        </a>
        <?php if ($user): ?>
        <div class="d-flex align-items-center">
            <span class="navbar-text text-white-50 me-3">Signed in as <strong class="text-white"><?= e($user['username']) ?></strong></span>
            <a class="btn btn-sm btn-outline-light" href="logout.php">Log out</a>
        </div>
        <?php endif; ?>
    </div>
</nav>
<main class="container pb-5">
<?php
}

function render_footer(): void
{
    ?>
</main>
<footer class="text-center text-muted py-4 small">
    Inventory Manager &middot; built with PHP, MySQL &amp; Bootstrap
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<?php
}

function flash(string $message, string $type = 'success'): void
{
    start_session();
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function render_flash(): void
{
    start_session();
    if (empty($_SESSION['flash'])) {
        return;
    }
    foreach ($_SESSION['flash'] as $item) {
        printf(
            '<div class="alert alert-%s alert-dismissible fade show" role="alert">%s<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>',
            e($item['type']),
            e($item['message'])
        );
    }
    unset($_SESSION['flash']);
}
