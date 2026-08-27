<?php
$pageTitle = 'Login';
require_once __DIR__ . '/config.php';

$email = '';
$errors = [];

if (is_logged_in()) {
    set_flash('success', 'You are already signed in.');
    redirect('checkout.php');
}

$redirectTarget = safe_redirect_target($_GET['redirect'] ?? null, 'checkout.php');

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $email = sanitize_email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        if ($password === '') {
            $errors[] = 'Enter your password.';
        }

        if (!$errors) {
            $stmt = db()->prepare('SELECT id, fullname, email, password FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, (string) $user['password'])) {
                $errors[] = 'Invalid email or password.';
            } else {
                login_user($user);
                set_flash('success', 'Login successful.');
                redirect($redirectTarget);
            }
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="auth-wrap">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="hero-panel h-100">
                    <div class="section-kicker mb-3 text-white-50">Customer Login</div>
                    <h1 class="mb-3">Sign in to continue shopping.</h1>
                    <p class="mb-4">Returning customers can sign in to access their account, basket, and checkout.</p>
                    <ul class="list-unstyled mb-0 opacity-75">
                        <li class="mb-2">- Access your account</li>
                        <li class="mb-2">- Review your basket</li>
                        <li class="mb-2">- Continue to checkout</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="auth-panel p-4 p-md-5">
                    <h2 class="mb-3">Login</h2>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo h($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="login.php?redirect=<?php echo h($redirectTarget); ?>" novalidate>
                        <?php echo csrf_input(); ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo h($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">Login</button>
                            <a href="register.php" class="btn btn-outline-dark">Create an account</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
