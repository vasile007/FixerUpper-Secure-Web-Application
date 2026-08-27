<?php
declare(strict_types=1);

date_default_timezone_set('Europe/London');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_start();

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3307');
define('DB_NAME', getenv('DB_NAME') ?: 'fixerupper_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
const SESSION_TIMEOUT = 1800;

function product_image_url(string $name, string $image): string
{
    $images = [
        'Cordless Drill' => 'https://media.diy.com/is/image/Kingfisher/black-decker-18v-li-ion-brushed-cordless-combi-drill-1-x-2ah-kfbcd701d1k~5035048700693_05c_bq?$MOB_PREV$&$width=1200&$height=1200',
        'Hammer' => 'https://media.diy.com/is/image/Kingfisher/carbon-steel-soft-grip-claw-hammer-16oz-454g~3663602817857_01bq?$MOB_PREV$&$width=1200&$height=1200',
        'Screwdriver Set' => 'https://media.diy.com/is/image/Kingfisher/magnusson-standard-mixed-screwdriver-set-6-piece-scs03~3663602818793_01bq?$MOB_PREV$&$width=1200&$height=1200',
        'Circular Saw' => 'https://media.diy.com/is/image/Kingfisher/mac-allister-1200w-240v-165mm-corded-circular-saw-mcs1200~5059340252063_01c_bq?$MOB_PREV$&$width=1200&$height=1200',
        'Sander' => 'https://media.diy.com/is/image/Kingfisher/black-decker-55w-240v-corded-detail-sander-bew230-gb~5035048704127_01c_bq?$MOB_PREV$&$width=1200&$height=1200',
        'Tool Kit' => 'https://media.diy.com/is/image/KingfisherDigital/uccebuy-home-repair-tool-kit-with-storage-case-diy-automotive-use~5060777718015_01c_MP?$MOB_PREV$&$width=1200&$height=1200',
        'Power Drill' => 'https://media.diy.com/is/image/Kingfisher/bosch-power-for-all-18v-li-ion-cordless-drill-driver-1-x-2ah-easydrill-18v-40~4053423232578_07c_bq?$MOB_PREV$&$width=1200&$height=1200',
        'Angle Grinder' => 'https://media.diy.com/is/image/Kingfisher/bosch-750w-240v-115mm-corded-angle-grinder-universalgrind-750-115~4059952603858_01c_bq?$MOB_PREV$&$width=1200&$height=1200',
        'Measuring Tape' => 'https://media.diy.com/is/image/Kingfisher/stanley-5m-tape-measure-0-30-696~3253560306960_01bq?$MOB_PREV$&$width=1200&$height=1200',
        'Wrench Set' => 'https://media.diy.com/is/image/KingfisherDigital/ozzo-combination-spanner-set-25-piece-metric-open-ring-end-wrench~5059482077890_01c_MP?$MOB_PREV$&$width=1200&$height=1200',
        'Electric Screwdriver' => 'https://media.diy.com/is/image/Kingfisher/bosch-3-6v-li-ion-cordless-screwdriver-1-x-2ah-ixo-7~4053423234824_02c_bq?$MOB_PREV$&$width=1200&$height=1200',
        'Safety Equipment' => 'https://media.diy.com/is/image/KingfisherDigital/technicians-ppe-kit-with-ear-defenders-rigger-safety-glasses-cap-pack-qty-1~5056839828662_01c_MP?$MOB_PREV$&$width=1200&$height=1200',
    ];

    return $images[$name] ?? $image;
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        ensure_order_fulfilment_column($pdo);
    } catch (PDOException $e) {
        exit('Database connection failed: ' . $e->getMessage());
    }

    return $pdo;
}

function ensure_order_fulfilment_column(PDO $pdo): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $columns = [
        'fulfilment_method' => "ADD COLUMN fulfilment_method VARCHAR(20) NOT NULL DEFAULT 'collection' AFTER user_id",
        'customer_name' => "ADD COLUMN customer_name VARCHAR(150) NOT NULL DEFAULT '' AFTER fulfilment_method",
        'address_line1' => "ADD COLUMN address_line1 VARCHAR(255) NOT NULL DEFAULT '' AFTER customer_name",
        'address_line2' => "ADD COLUMN address_line2 VARCHAR(255) NOT NULL DEFAULT '' AFTER address_line1",
        'city' => "ADD COLUMN city VARCHAR(100) NOT NULL DEFAULT '' AFTER address_line2",
        'postcode' => "ADD COLUMN postcode VARCHAR(20) NOT NULL DEFAULT '' AFTER city",
    ];

    $columnExists = $pdo->prepare(
        'SELECT COUNT(*) AS column_count
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = :schema
           AND TABLE_NAME = :table
           AND COLUMN_NAME = :column'
    );

    foreach ($columns as $column => $alterClause) {
        $columnExists->execute([
            'schema' => DB_NAME,
            'table' => 'orders',
            'column' => $column,
        ]);

        if ((int) $columnExists->fetchColumn() === 0) {
            $pdo->exec('ALTER TABLE `orders` ' . $alterClause);
        }
    }

    $checked = true;
}

function normalize_product_rows(array $products): array
{
    foreach ($products as &$product) {
        $product['image'] = product_image_url((string) ($product['name'] ?? ''), (string) ($product['image'] ?? ''));
    }
    unset($product);

    return $products;
}

function h(?string $value): string
{
    // XSS protection: escape all dynamic output before sending it to the browser.
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf_token(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function sanitize_text(?string $value): string
{
    return trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)) ?? '');
}

function sanitize_email(?string $value): string
{
    return trim((string) $value);
}

function format_price(float $amount): string
{
    return '&pound;' . number_format($amount, 2);
}

function password_errors(string $password): array
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }

    return $errors;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = (string) $user['fullname'];
    $_SESSION['user_email'] = (string) $user['email'];
    $_SESSION['last_activity'] = time();
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function enforce_session_timeout(): void
{
    if (!is_logged_in()) {
        return;
    }

    $lastActivity = (int) ($_SESSION['last_activity'] ?? time());
    if ((time() - $lastActivity) > SESSION_TIMEOUT) {
        logout_user();
        session_start();
        set_flash('warning', 'Your session expired after 30 minutes of inactivity. Please log in again.');
        return;
    }

    $_SESSION['last_activity'] = time();
}

function require_login(string $redirectTo = 'login.php'): void
{
    if (!is_logged_in()) {
        set_flash('warning', 'Please log in to continue to checkout.');
        redirect($redirectTo);
    }
}

function safe_redirect_target(?string $target, string $default = 'checkout.php'): string
{
    $allowed = ['index.php', 'products.php', 'cart.php', 'checkout.php', 'account.php'];
    return in_array($target, $allowed, true) ? $target : $default;
}

function get_products(?int $limit = null): array
{
    $sql = 'SELECT id, name, description, price, image FROM products ORDER BY id ASC';
    if ($limit !== null) {
        $sql .= ' LIMIT ' . (int) $limit;
    }

    return normalize_product_rows(db()->query($sql)->fetchAll());
}

function get_product_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT id, name, description, price, image FROM products WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    return $product ? normalize_product_rows([$product])[0] : null;
}

function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int
{
    $count = 0;
    foreach (cart_items() as $quantity) {
        $count += max(0, (int) $quantity);
    }
    return $count;
}

function add_to_cart(int $productId, int $quantity = 1): void
{
    if ($quantity < 1) {
        $quantity = 1;
    }

    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }

    $_SESSION['cart'][$productId] += $quantity;
}

function update_cart_item(int $productId, int $quantity): void
{
    if ($quantity < 1) {
        remove_cart_item($productId);
        return;
    }

    $_SESSION['cart'][$productId] = $quantity;
}

function remove_cart_item(int $productId): void
{
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

function clear_cart(): void
{
    unset($_SESSION['cart']);
}

function cart_details(): array
{
    $items = [];
    $cart = cart_items();

    foreach ($cart as $productId => $quantity) {
        $product = get_product_by_id((int) $productId);
        if (!$product) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $qty = max(1, (int) $quantity);
        $price = (float) $product['price'];
        $items[] = [
            'id' => (int) $product['id'],
            'name' => (string) $product['name'],
            'description' => (string) $product['description'],
            'price' => $price,
            'image' => (string) $product['image'],
            'quantity' => $qty,
            'subtotal' => $price * $qty,
        ];
    }

    return $items;
}

function cart_total(array $items): float
{
    $total = 0.0;
    foreach ($items as $item) {
        $total += (float) $item['subtotal'];
    }
    return $total;
}

function get_order_with_items(int $orderId, int $userId): ?array
{
    $orderStmt = db()->prepare('SELECT id, user_id, fulfilment_method, customer_name, address_line1, address_line2, city, postcode, order_date FROM `orders` WHERE id = :id AND user_id = :user_id LIMIT 1');
    $orderStmt->execute(['id' => $orderId, 'user_id' => $userId]);
    $order = $orderStmt->fetch();

    if (!$order) {
        return null;
    }

    $itemsStmt = db()->prepare(
        'SELECT oi.quantity, p.name, p.price
         FROM order_items oi
         INNER JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id = :order_id
         ORDER BY oi.id ASC'
    );
    $itemsStmt->execute(['order_id' => $orderId]);
    $items = $itemsStmt->fetchAll();

    $total = 0.0;
    foreach ($items as $item) {
        $total += ((float) $item['price']) * ((int) $item['quantity']);
    }

    return [
        'order' => $order,
        'items' => $items,
        'total' => $total,
    ];
}

enforce_session_timeout();
