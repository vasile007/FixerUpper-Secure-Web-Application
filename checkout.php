<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/config.php';

if (!is_logged_in()) {
    set_flash('warning', 'Please log in or register before confirming your order.');
    redirect('login.php?redirect=checkout.php');
}

$items = cart_details();
if (empty($items)) {
    set_flash('warning', 'Your cart is empty. Add products before checkout.');
    redirect('products.php');
}

$total = cart_total($items);
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="section-kicker">Checkout</div>
            <h1 class="mb-2">Confirm your order</h1>
            <p class="text-muted mb-0">You are signed in as <?php echo h($_SESSION['user_name'] ?? ''); ?>. Review the summary, then confirm the purchase.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="content-card p-4">
                <h4 class="mb-3">Order details</h4>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo h($item['name']); ?></div>
                                        <div class="text-muted small"><?php echo h($item['description']); ?></div>
                                    </td>
                                    <td class="text-end"><?php echo (int) $item['quantity']; ?></td>
                                    <td class="text-end fw-bold"><?php echo format_price((float) $item['subtotal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
                <div class="order-summary p-4">
                    <h4 class="mb-3">Order summary</h4>
                <div class="d-flex justify-content-between mb-2">
                    <span>Customer</span>
                    <span><?php echo h($_SESSION['user_name'] ?? ''); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Email</span>
                    <span><?php echo h($_SESSION['user_email'] ?? ''); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Items</span>
                    <span><?php echo count($items); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total quantity</span>
                    <span><?php echo cart_count(); ?></span>
                </div>
                <hr class="border-white border-opacity-25">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5">Total payable</span>
                    <span class="fs-3 fw-bold"><?php echo format_price($total); ?></span>
                </div>

                <form method="post" action="confirm_order.php">
                    <?php echo csrf_input(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="customer_name">Full name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo h($_SESSION['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="address_line1">Address line 1</label>
                        <input type="text" class="form-control" id="address_line1" name="address_line1" autocomplete="address-line1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="address_line2">Address line 2</label>
                        <input type="text" class="form-control" id="address_line2" name="address_line2" autocomplete="address-line2">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="city">Town / City</label>
                            <input type="text" class="form-control" id="city" name="city" autocomplete="address-level2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="postcode">Postcode</label>
                            <input type="text" class="form-control text-uppercase" id="postcode" name="postcode" autocomplete="postal-code" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="fulfilment_method">Collection or delivery</label>
                        <select class="form-select" id="fulfilment_method" name="fulfilment_method" required>
                            <option value="collection">Collection</option>
                            <option value="delivery">Delivery</option>
                        </select>
                        <div class="form-text text-white-50">Choose how you want to receive your order.</div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">Confirm Order</button>
                </form>

                <a href="cart.php" class="btn btn-outline-light w-100 mt-3">Back to Cart</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
