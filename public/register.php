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
        $confirm = (string) ($_POST['confirm'] ?? '');

        if ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $result = register_user($username, $password);
            if ($result['ok']) {
                login_user($result);
                flash('Welcome, ' . $result['username'] . '! Your account is ready.');
                header('Location: dashboard.php');
                exit;
            }
            $error = $result['error'];
        }
    }
}

render_header('Register');
?>
<div class="card shadow-sm auth-card">
    <div class="card-body p-4">
        <h1 class="h4 mb-3 text-center">Create your account</h1>
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
                <div class="form-text">At least 6 characters.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirm">Confirm password</label>
                <input type="password" class="form-control" id="confirm" name="confirm" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Register</button>
        </form>
        <p class="text-center mt-3 mb-0">
            Already have an account? <a href="login.php">Log in</a>
        </p>
    </div>
</div>
<?php
render_footer();
