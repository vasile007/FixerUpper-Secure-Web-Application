<?php
$pageTitle = 'Account Dashboard';
require_once __DIR__ . '/config.php';

if (!is_logged_in()) {
    set_flash('warning', 'Please log in to view your dashboard.');
    redirect('login.php?redirect=account.php');
}

$userId = (int) $_SESSION['user_id'];

$userStmt = db()->prepare('SELECT fullname, email, created_at FROM users WHERE id = :id LIMIT 1');
$userStmt->execute(['id' => $userId]);
$user = $userStmt->fetch() ?: [
    'fullname' => (string) ($_SESSION['user_name'] ?? 'Customer'),
    'email' => (string) ($_SESSION['user_email'] ?? ''),
    'created_at' => null,
];

$ordersStmt = db()->prepare(
    'SELECT
        o.id,
        o.fulfilment_method,
        o.order_date,
        COUNT(oi.id) AS line_count,
        SUM(oi.quantity) AS units,
        SUM(oi.quantity * p.price) AS total
     FROM `orders` o
     INNER JOIN order_items oi ON oi.order_id = o.id
     INNER JOIN products p ON p.id = oi.product_id
     WHERE o.user_id = :user_id
     GROUP BY o.id, o.order_date
     ORDER BY o.order_date DESC, o.id DESC'
);
$ordersStmt->execute(['user_id' => $userId]);
$orders = $ordersStmt->fetchAll();

$orderCount = count($orders);
$unitCount = 0;
$lifetimeTotal = 0.0;

foreach ($orders as &$order) {
    $order['id'] = (int) $order['id'];
    $order['fulfilment_method'] = (string) $order['fulfilment_method'];
    $order['line_count'] = (int) $order['line_count'];
    $order['units'] = (int) $order['units'];
    $order['total'] = (float) $order['total'];
    $unitCount += $order['units'];
    $lifetimeTotal += $order['total'];
}
unset($order);

$recentOrders = $orders;

require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="row g-4 align-items-stretch">
        <div class="col-lg-4">
            <div class="hero-panel h-100">
                <div class="section-kicker mb-3 text-white-50">Account</div>
                <h1 class="mb-3">Your dashboard</h1>
                <p class="mb-4">Check your orders, see what you have bought, and continue shopping from one place.</p>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="mini-stat">
                            <div class="fs-2 fw-bold"><?php echo (int) $orderCount; ?></div>
                            <div class="text-white-50">Orders</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini-stat">
                            <div class="fs-2 fw-bold"><?php echo (int) $unitCount; ?></div>
                            <div class="text-white-50">Items</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mini-stat">
                            <div class="fs-4 fw-bold"><?php echo format_price($lifetimeTotal); ?></div>
                            <div class="text-white-50">Total spent</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="fw-bold mb-1"><?php echo h((string) $user['fullname']); ?></div>
                    <div class="text-white-50 small"><?php echo h((string) $user['email']); ?></div>
                    <?php if (!empty($user['created_at'])): ?>
                        <div class="text-white-50 small mt-2">Member since <?php echo h((string) $user['created_at']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="content-card p-4 p-md-5 h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div>
                        <div class="section-kicker">Recent Orders</div>
                        <h2 class="mb-0">Order history</h2>
                    </div>
                    <a href="products.php" class="btn btn-warning fw-bold">Continue Shopping</a>
                </div>

                <?php if (empty($recentOrders)): ?>
                    <div class="text-center py-5">
                        <h3 class="mb-3">No orders yet</h3>
                        <p class="text-muted mb-4">When you place your first order, it will appear here.</p>
                        <a href="products.php" class="btn btn-warning btn-lg fw-bold">Browse Products</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Order</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Fulfilment</th>
                                    <th scope="col">Lines</th>
                                    <th scope="col">Units</th>
                                    <th scope="col">Total</th>
                                    <th scope="col" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo (int) $order['id']; ?></td>
                                        <td><?php echo h((string) $order['order_date']); ?></td>
                                        <td><?php echo h(ucfirst($order['fulfilment_method'])); ?></td>
                                        <td><?php echo (int) $order['line_count']; ?></td>
                                        <td><?php echo (int) $order['units']; ?></td>
                                        <td class="fw-bold"><?php echo format_price((float) $order['total']); ?></td>
                                        <td class="text-end">
                                            <a href="confirm_order.php?order_id=<?php echo (int) $order['id']; ?>" class="btn btn-sm btn-outline-primary">View order</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
