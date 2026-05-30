<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/layout.php';

$user = require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;
$error = null;

$product = ['id' => 0, 'sku' => '', 'name' => '', 'description' => '', 'price' => '', 'quantity' => ''];

if ($isEdit) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash('Product not found.', 'warning');
        header('Location: dashboard.php');
        exit;
    }
    $product = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? null)) {
        $error = 'Invalid session token. Please try again.';
    } else {
        $product['sku'] = trim((string) ($_POST['sku'] ?? ''));
        $product['name'] = trim((string) ($_POST['name'] ?? ''));
        $product['description'] = trim((string) ($_POST['description'] ?? ''));
        $product['price'] = (string) ($_POST['price'] ?? '');
        $product['quantity'] = (string) ($_POST['quantity'] ?? '');

        $price = (float) $product['price'];
        $quantity = (int) $product['quantity'];

        if ($product['sku'] === '' || $product['name'] === '') {
            $error = 'SKU and name are required.';
        } elseif ($price < 0 || $quantity < 0) {
            $error = 'Price and quantity cannot be negative.';
        } else {
            // Enforce unique SKU (excluding the current row on edit).
            $dup = db()->prepare('SELECT id FROM products WHERE sku = ? AND id <> ?');
            $dup->execute([$product['sku'], $id]);
            if ($dup->fetch()) {
                $error = 'A product with that SKU already exists.';
            } elseif ($isEdit) {
                $stmt = db()->prepare(
                    'UPDATE products SET sku = ?, name = ?, description = ?, price = ?, quantity = ? WHERE id = ?'
                );
                $stmt->execute([$product['sku'], $product['name'], $product['description'], $price, $quantity, $id]);
                flash('Product updated.');
                header('Location: dashboard.php');
                exit;
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO products (sku, name, description, price, quantity) VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([$product['sku'], $product['name'], $product['description'], $price, $quantity]);
                flash('Product added.');
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}

render_header($isEdit ? 'Edit product' : 'Add product', $user);
?>
<div class="row">
    <div class="col-lg-7 col-xl-6">
        <div class="d-flex align-items-center mb-3">
            <a class="btn btn-link px-0 me-2" href="dashboard.php">&larr; Back</a>
            <h1 class="h3 mb-0"><?= $isEdit ? 'Edit product' : 'Add product' ?></h1>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <div class="card shadow-sm"><div class="card-body">
            <form method="post" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div class="mb-3">
                    <label class="form-label" for="sku">SKU</label>
                    <input class="form-control" id="sku" name="sku" value="<?= e((string) $product['sku']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" id="name" name="name" value="<?= e((string) $product['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= e((string) $product['description']) ?></textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label" for="price">Price ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?= e((string) $product['price']) ?>" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label" for="quantity">Quantity</label>
                        <input type="number" min="0" class="form-control" id="quantity" name="quantity" value="<?= e((string) $product['quantity']) ?>" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save changes' : 'Add product' ?></button>
                    <a class="btn btn-outline-secondary" href="dashboard.php">Cancel</a>
                </div>
            </form>
        </div></div>
    </div>
</div>
<?php
render_footer();
