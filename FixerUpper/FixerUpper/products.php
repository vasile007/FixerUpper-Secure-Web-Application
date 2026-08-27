<?php
$pageTitle = 'Products';
require_once __DIR__ . '/includes/header.php';
$products = get_products();

function product_category_label(array $product): string
{
    $name = strtolower((string) ($product['name'] ?? ''));
    $description = strtolower((string) ($product['description'] ?? ''));

    if (str_contains($name, 'drill') || str_contains($name, 'saw') || str_contains($name, 'sander') || str_contains($name, 'grinder') || str_contains($description, 'electric')) {
        return 'power-tools';
    }

    if (str_contains($name, 'tape') || str_contains($name, 'measuring')) {
        return 'measurement';
    }

    if (str_contains($name, 'safety') || str_contains($name, 'goggle') || str_contains($name, 'ear') || str_contains($name, 'glove')) {
        return 'safety';
    }

    if (str_contains($name, 'kit')) {
        return 'kits';
    }

    return 'hand-tools';
}
?>

<section class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="section-kicker">Catalogue</div>
            <h1 class="mb-2">Products and tools</h1>
            <p class="text-muted mb-0">Browse the full product catalogue and add items directly to your session-based shopping cart.</p>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <button type="button" class="btn btn-dark btn-sm filter-btn active" data-filter="all">All</button>
        <button type="button" class="btn btn-outline-dark btn-sm filter-btn" data-filter="power-tools">Power tools</button>
        <button type="button" class="btn btn-outline-dark btn-sm filter-btn" data-filter="hand-tools">Hand tools</button>
        <button type="button" class="btn btn-outline-dark btn-sm filter-btn" data-filter="measurement">Measurement</button>
        <button type="button" class="btn btn-outline-dark btn-sm filter-btn" data-filter="safety">Safety</button>
        <button type="button" class="btn btn-outline-dark btn-sm filter-btn" data-filter="kits">Kits</button>
    </div>

    <div class="alert alert-light border d-none" id="products-empty" role="status">
        No products match this category.
    </div>

    <div class="row g-4" id="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="col-md-6 col-lg-4 product-item" data-category="<?php echo h(product_category_label($product)); ?>">
                <article class="product-card p-3">
                    <div class="product-image-wrap mb-3">
                        <img src="<?php echo h($product['image']); ?>" alt="<?php echo h($product['name']); ?>">
                    </div>
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <h5 class="mb-1"><?php echo h($product['name']); ?></h5>
                        <span class="price"><?php echo format_price((float) $product['price']); ?></span>
                    </div>
                    <p class="text-muted small mb-3"><?php echo h($product['description']); ?></p>
                    <form method="post" action="cart.php" class="d-grid gap-2">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                        <input type="hidden" name="return_to" value="products.php">
                        <label class="form-label small mb-0" for="qty-<?php echo (int) $product['id']; ?>">Quantity</label>
                        <input id="qty-<?php echo (int) $product['id']; ?>" type="number" min="1" max="99" class="form-control" name="quantity" value="1">
                        <button type="submit" class="btn btn-warning fw-bold">Add To Cart</button>
                    </form>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
(function () {
    const buttons = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.product-item');
    const emptyState = document.getElementById('products-empty');

    function applyFilter(filter) {
        let visible = 0;

        items.forEach((item) => {
            const match = filter === 'all' || item.dataset.category === filter;
            item.classList.toggle('d-none', !match);
            if (match) {
                visible += 1;
            }
        });

        emptyState.classList.toggle('d-none', visible !== 0);
    }

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            buttons.forEach((btn) => {
                btn.classList.remove('btn-dark', 'active');
                btn.classList.add('btn-outline-dark');
            });

            button.classList.remove('btn-outline-dark');
            button.classList.add('btn-dark', 'active');
            applyFilter(button.dataset.filter || 'all');
        });
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
