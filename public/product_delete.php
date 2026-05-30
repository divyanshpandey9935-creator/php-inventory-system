<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/layout.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf'] ?? null)) {
    flash('Invalid delete request.', 'danger');
    header('Location: dashboard.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    flash('Product deleted.');
}

header('Location: dashboard.php');
exit;
