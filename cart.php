<?php
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/config.php';

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Invalid security token. Please try again.');
        redirect('cart.php');
    }

    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $product = get_product_by_id($productId);

        if (!$product) {
            set_flash('danger', 'The selected product could not be found.');
        } else {
            add_to_cart($productId, $quantity);
            set_flash('success', h($product['name']) . ' was added to your cart.');
        }

        $returnTo = safe_redirect_target($_POST['return_to'] ?? null, 'products.php');
        redirect($returnTo);
    }

    if ($action === 'update') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        update_cart_item($productId, $quantity);
        set_flash('success', 'Your cart has been updated.');
        redirect('cart.php');
    }

    if ($action === 'remove') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        remove_cart_item($productId);
        set_flash('success', 'The item was removed from your cart.');
        redirect('cart.php');
    }

    if ($action === 'clear') {
        clear_cart();
        set_flash('success', 'Your cart has been cleared.');
        redirect('cart.php');
    }
}

$items = cart_details();
$total = cart_total($items);
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="section-kicker">Basket</div>
            <h1 class="mb-2">Your shopping cart</h1>
            <p class="text-muted mb-0">Review your selected products, update quantities, or proceed to secure checkout.</p>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <div class="cart-card p-5 text-center">
            <h3 class="mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Add products and tools from the catalogue to start shopping.</p>
            <a href="products.php" class="btn btn-warning btn-lg">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-card p-3 p-md-4">
                    <table class="table table-cart align-middle mb-4">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Unit price</th>
                                    <th scope="col" style="width: 140px;">Qty</th>
                                    <th scope="col">Line total</th>
                                    <th scope="col" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo h($item['image']); ?>" alt="<?php echo h($item['name']); ?>" class="cart-thumb">
                                                <div>
                                                    <div class="fw-bold"><?php echo h($item['name']); ?></div>
                                                    <div class="text-muted small"><?php echo h($item['description']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo format_price((float) $item['price']); ?></td>
                                        <td>
                                            <form method="post" action="cart.php" class="d-flex gap-2 align-items-center">
                                                <?php echo csrf_input(); ?>
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?php echo (int) $item['id']; ?>">
                                                <input type="number" min="1" max="99" class="form-control" name="quantity" value="<?php echo (int) $item['quantity']; ?>" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="fw-bold"><?php echo format_price((float) $item['subtotal']); ?></td>
                                        <td class="text-end">
                                            <form method="post" action="cart.php" class="d-inline">
                                                <?php echo csrf_input(); ?>
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?php echo (int) $item['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                    </table>

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <form method="post" action="cart.php" class="d-inline">
                            <?php echo csrf_input(); ?>
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Clear your cart?');">Clear Cart</button>
                        </form>
                        <a href="checkout.php" class="btn btn-warning fw-bold">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="order-summary p-4 sticky-top" style="top: 100px;">
                    <h4 class="mb-3">Order summary</h4>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Items</span>
                        <span><?php echo count($items); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Units</span>
                        <span><?php echo cart_count(); ?></span>
                    </div>
                    <hr class="border-white border-opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5">Total to pay</span>
                        <span class="fs-3 fw-bold"><?php echo format_price($total); ?></span>
                    </div>
                    <p class="text-white-50 mt-3 mb-0">Checkout is available once you sign in. Session cart contents are preserved until logout or timeout.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
