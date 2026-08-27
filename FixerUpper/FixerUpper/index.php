<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
$featuredProducts = get_products(4);
?>

<section class="hero">
    <div class="container hero-content">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="hero-badge mb-3">FixerUpper</div>
                <h1 class="fw-bold mb-3">FixerUpper online store for tools and essentials.</h1>
                <p class="lead mb-4">Browse practical products, add them to your basket, and complete your order in one place.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="products.php" class="btn btn-warning btn-lg px-4">Browse Products</a>
                    <a href="register.php" class="btn btn-outline-light btn-lg px-4">Create Account</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-panel">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="mini-stat">
                                <div class="fs-1 fw-bold">12+</div>
                                <div class="text-white-50">Products available</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mini-stat">
                                <div class="fs-1 fw-bold">100%</div>
                                <div class="text-white-50">Online store</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mini-stat">
                                <div class="fw-bold mb-1">Simple shopping</div>
                                <div class="text-white-50">Built for browsing, cart management, checkout, and order confirmation.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card p-4 h-100">
                <div class="section-kicker mb-2">Shopping Cart</div>
                <h3>Session-based basket</h3>
                <p class="mb-0 text-muted">Customers can add items without logging in, then continue to checkout after authentication.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 h-100">
                <div class="section-kicker mb-2">Secure Login</div>
                <h3>Protected accounts</h3>
                <p class="mb-0 text-muted">Account access is handled securely to keep customer information protected.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 h-100">
                <div class="section-kicker mb-2">Checkout Flow</div>
                <h3>Clear order confirmation</h3>
                <p class="mb-0 text-muted">Orders are created inside a transaction and then the cart is securely emptied.</p>
            </div>
        </div>
    </div>
</section>

<section class="container pb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <div class="section-kicker">Featured</div>
            <h2 class="mb-0">Popular products and tools</h2>
        </div>
        <a href="products.php" class="btn btn-outline-dark">View all products</a>
    </div>

    <div class="row g-4">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-6 col-lg-3">
                <div class="product-card p-3">
                    <div class="product-image-wrap mb-3">
                        <img src="<?php echo h($product['image']); ?>" alt="<?php echo h($product['name']); ?>">
                    </div>
                    <h5><?php echo h($product['name']); ?></h5>
                    <p class="text-muted small mb-3"><?php echo h($product['description']); ?></p>
                    <form method="post" action="cart.php" class="d-grid gap-2">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                        <input type="hidden" name="return_to" value="index.php">
                        <label class="form-label small mb-0" for="home-qty-<?php echo (int) $product['id']; ?>">Quantity</label>
                        <input id="home-qty-<?php echo (int) $product['id']; ?>" type="number" min="1" max="99" class="form-control form-control-sm" name="quantity" value="1">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price"><?php echo format_price((float) $product['price']); ?></span>
                            <button type="submit" class="btn btn-sm btn-warning">Add To Cart</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
