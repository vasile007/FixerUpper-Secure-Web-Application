<?php
require_once __DIR__ . '/../config.php';

$pageTitle = $pageTitle ?? 'FixerUpper';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$flash = get_flash();
$cartCount = cart_count();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($pageTitle); ?> | FixerUpper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top site-nav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">FixerUpper</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'products.php' ? 'active' : ''; ?>" href="products.php">Products</a></li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'cart.php' ? 'active' : ''; ?>" href="cart.php">
                        Cart
                        <?php if ($cartCount > 0): ?>
                            <span class="badge text-bg-warning ms-1"><?php echo (int) $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><span class="nav-link text-light opacity-75">Hi, <?php echo h($_SESSION['user_name'] ?? ''); ?></span></li>
                    <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'account.php' ? 'active' : ''; ?>" href="account.php">Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-2" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'login.php' ? 'active' : ''; ?>" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="site-main">
    <?php if ($flash): ?>
        <div class="container pt-4">
            <div class="alert alert-<?php echo h($flash['type']); ?> alert-dismissible fade show shadow-sm" role="alert">
                <?php echo h($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
