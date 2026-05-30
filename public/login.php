<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/layout.php';

if (current_user()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        $error = 'Invalid session token. Please try again.';
    } else {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $result = authenticate($username, $password);
        if ($result['ok']) {
            login_user($result);
            flash('Welcome back, ' . $result['username'] . '!');
            header('Location: dashboard.php');
            exit;
        }
        $error = $result['error'];
    }
}

render_header('Log in');
?>
<div class="card shadow-sm auth-card">
    <div class="card-body p-4">
        <h1 class="h4 mb-3 text-center">Log in</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" id="username" name="username" value="<?= e($username) ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Log in</button>
        </form>
        <p class="text-center mt-3 mb-0">
            No account yet? <a href="register.php">Register</a>
        </p>
    </div>
</div>
<?php
render_footer();
