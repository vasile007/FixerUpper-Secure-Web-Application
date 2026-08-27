<?php
$pageTitle = 'Order Confirmation';
require_once __DIR__ . '/config.php';

if (!is_logged_in()) {
    set_flash('warning', 'Please log in before confirming your order.');
    redirect('login.php?redirect=checkout.php');
}

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Invalid security token. Please try again.');
        redirect('checkout.php');
    }

    $items = cart_details();
    if (empty($items)) {
        set_flash('warning', 'Your cart is empty. Add products before confirming an order.');
        redirect('products.php');
    }

    $fulfilmentMethod = (string) ($_POST['fulfilment_method'] ?? 'collection');
    if (!in_array($fulfilmentMethod, ['collection', 'delivery'], true)) {
        $fulfilmentMethod = 'collection';
    }

    $customerName = sanitize_text($_POST['customer_name'] ?? '');
    $addressLine1 = sanitize_text($_POST['address_line1'] ?? '');
    $addressLine2 = sanitize_text($_POST['address_line2'] ?? '');
    $city = sanitize_text($_POST['city'] ?? '');
    $postcode = strtoupper(sanitize_text($_POST['postcode'] ?? ''));

    if ($customerName === '' || $addressLine1 === '' || $city === '' || $postcode === '') {
        set_flash('warning', 'Please complete your name and address details before confirming the order.');
        redirect('checkout.php');
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $orderStmt = $pdo->prepare(
            'INSERT INTO `orders` (user_id, fulfilment_method, customer_name, address_line1, address_line2, city, postcode, order_date)
             VALUES (:user_id, :fulfilment_method, :customer_name, :address_line1, :address_line2, :city, :postcode, NOW())'
        );
        $orderStmt->execute([
            'user_id' => (int) $_SESSION['user_id'],
            'fulfilment_method' => $fulfilmentMethod,
            'customer_name' => $customerName,
            'address_line1' => $addressLine1,
            'address_line2' => $addressLine2,
            'city' => $city,
            'postcode' => $postcode,
        ]);

        $orderId = (int) $pdo->lastInsertId();
        $itemStmt = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity)
             VALUES (:order_id, :product_id, :quantity)'
        );

        foreach ($items as $item) {
            $itemStmt->execute([
                'order_id' => $orderId,
                'product_id' => (int) $item['id'],
                'quantity' => (int) $item['quantity'],
            ]);
        }

        $pdo->commit();
        clear_cart();
        redirect('confirm_order.php?order_id=' . $orderId);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        set_flash('danger', 'We could not process your order. Please try again.');
        redirect('checkout.php');
    }
}

$orderId = (int) ($_GET['order_id'] ?? 0);
$orderData = $orderId > 0 ? get_order_with_items($orderId, (int) $_SESSION['user_id']) : null;

if (!$orderData) {
    set_flash('warning', 'No completed order was found.');
    redirect('products.php');
}

$order = $orderData['order'];
$orderItems = $orderData['items'];
$total = (float) $orderData['total'];
$fulfilmentMethod = (string) ($order['fulfilment_method'] ?? 'collection');
$fulfilmentLabel = $fulfilmentMethod === 'delivery' ? 'Delivery' : 'Collection';
$addressLines = array_values(array_filter([
    (string) ($order['address_line1'] ?? ''),
    (string) ($order['address_line2'] ?? ''),
    trim((string) ($order['city'] ?? '') . ', ' . (string) ($order['postcode'] ?? '')),
], static fn ($line) => trim($line) !== ''));
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card p-4 p-md-5 text-center">
                <div class="badge text-bg-success mb-3 px-3 py-2">Order placed successfully</div>
                <h1 class="mb-3">Thank you for your purchase, <?php echo h($_SESSION['user_name'] ?? 'customer'); ?>.</h1>
                <p class="text-muted mb-4">Your order has been recorded securely in the database. A confirmation reference is shown below.</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="section-kicker">Order ID</div>
                            <div class="fs-4 fw-bold">#<?php echo (int) $order['id']; ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="section-kicker">Order Date</div>
                            <div class="fs-6 fw-bold"><?php echo h((string) $order['order_date']); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="section-kicker">Total</div>
                            <div class="fs-4 fw-bold"><?php echo format_price($total); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="section-kicker">Fulfilment</div>
                            <div class="fs-4 fw-bold"><?php echo h($fulfilmentLabel); ?></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="stat-card p-4 text-start">
                            <div class="section-kicker mb-2"><?php echo h($fulfilmentLabel); ?> Details</div>
                            <div class="fw-bold"><?php echo h((string) ($order['customer_name'] ?? '')); ?></div>
                            <div class="text-muted mt-1">
                                <?php foreach ($addressLines as $line): ?>
                                    <div><?php echo h($line); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive text-start">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?php echo h($item['name']); ?></td>
                                    <td class="text-end"><?php echo (int) $item['quantity']; ?></td>
                                    <td class="text-end"><?php echo format_price(((float) $item['price']) * ((int) $item['quantity'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                    <a href="products.php" class="btn btn-warning btn-lg fw-bold">Continue Shopping</a>
                    <a href="account.php" class="btn btn-outline-dark btn-lg">View Dashboard</a>
                    <a href="logout.php" class="btn btn-outline-dark btn-lg">Logout</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
