<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/layout.php';

$user = require_login();

$search = trim((string) ($_GET['q'] ?? ''));
if ($search !== '') {
    $stmt = db()->prepare(
        'SELECT * FROM products WHERE name LIKE :q OR sku LIKE :q ORDER BY created_at DESC'
    );
    $stmt->execute([':q' => '%' . $search . '%']);
} else {
    $stmt = db()->query('SELECT * FROM products ORDER BY created_at DESC');
}
$products = $stmt->fetchAll();

$totalItems = count($products);
$totalUnits = array_sum(array_map(static fn ($p) => (int) $p['quantity'], $products));
$totalValue = array_sum(array_map(static fn ($p) => (float) $p['price'] * (int) $p['quantity'], $products));

render_header('Dashboard', $user);
render_flash();
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Products</h1>
    <a class="btn btn-primary" href="product_form.php">&#43; Add product</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-bg-light h-100"><div class="card-body">
            <div class="text-muted small">Distinct products</div>
            <div class="h4 mb-0"><?= number_format($totalItems) ?></div>
        </div></div>
    </div>
    <div class="col-sm-4">
        <div class="card text-bg-light h-100"><div class="card-body">
            <div class="text-muted small">Total units in stock</div>
            <div class="h4 mb-0"><?= number_format($totalUnits) ?></div>
        </div></div>
    </div>
    <div class="col-sm-4">
        <div class="card text-bg-light h-100"><div class="card-body">
            <div class="text-muted small">Inventory value</div>
            <div class="h4 mb-0">$<?= number_format($totalValue, 2) ?></div>
        </div></div>
    </div>
</div>

<form class="mb-3" method="get">
    <div class="input-group">
        <input class="form-control" type="search" name="q" placeholder="Search by name or SKU" value="<?= e($search) ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
        <?php if ($search !== ''): ?><a class="btn btn-outline-secondary" href="dashboard.php">Clear</a><?php endif; ?>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Value</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$products): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">
                    No products yet. <a href="product_form.php">Add your first product</a>.
                </td></tr>
            <?php else: foreach ($products as $p): ?>
                <tr>
                    <td><code><?= e($p['sku']) ?></code></td>
                    <td>
                        <div class="fw-semibold"><?= e($p['name']) ?></div>
                        <?php if (!empty($p['description'])): ?>
                            <div class="text-muted small"><?= e($p['description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">$<?= number_format((float) $p['price'], 2) ?></td>
                    <td class="text-end <?= (int) $p['quantity'] <= 5 ? 'low-stock' : '' ?>"><?= (int) $p['quantity'] ?></td>
                    <td class="text-end">$<?= number_format((float) $p['price'] * (int) $p['quantity'], 2) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="product_form.php?id=<?= (int) $p['id'] ?>">Edit</a>
                        <form class="d-inline" method="post" action="product_delete.php"
                              onsubmit="return confirm('Delete &quot;<?= e($p['name']) ?>&quot;?');">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
render_footer();
