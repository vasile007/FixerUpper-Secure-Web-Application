<?php
$pageTitle = 'Register';
require_once __DIR__ . '/config.php';

$errors = [];
$fullname = '';
$email = '';

if (is_logged_in()) {
    set_flash('success', 'You are already signed in.');
    redirect('checkout.php');
}

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $fullname = sanitize_text($_POST['fullname'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($fullname === '' || strlen($fullname) < 2) {
            $errors[] = 'Enter your full name.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        $passwordErrors = password_errors($password);
        foreach ($passwordErrors as $error) {
            $errors[] = $error;
        }
        if ($confirmPassword === '' || $password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (!$errors) {
            $stmt = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = db()->prepare(
                    'INSERT INTO users (fullname, email, password, created_at)
                     VALUES (:fullname, :email, :password, NOW())'
                );
                $insert->execute([
                    'fullname' => $fullname,
                    'email' => $email,
                    'password' => $hash,
                ]);

                set_flash('success', 'Registration successful. Please log in to continue.');
                redirect('login.php');
            }
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="auth-wrap">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="hero-panel h-100">
                    <div class="section-kicker mb-3 text-white-50">Create Account</div>
                    <h1 class="mb-3">Register safely with strong password rules.</h1>
                    <p class="mb-4">The registration form validates passwords in JavaScript for instant feedback and again in PHP before storing the account with a hashed password.</p>
                    <div class="feature-pill mb-3">At least 8 characters</div><br>
                    <div class="feature-pill mb-3">Uppercase, lowercase, and number</div><br>
                    <div class="feature-pill">Stored securely with password_hash()</div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="auth-panel p-4 p-md-5">
                    <h2 class="mb-3">Register</h2>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo h($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="registerForm" method="post" action="register.php" novalidate>
                        <?php echo csrf_input(); ?>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo h($fullname); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo h($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required aria-describedby="passwordHelp passwordFeedback">
                            <div id="passwordHelp" class="form-text">Use Password1, Secure2026, or MyAccount123 as examples.</div>
                            <div id="passwordFeedback" class="invalid-feedback d-block mt-2" style="display:none;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">Create account</button>
                            <a href="login.php" class="btn btn-outline-dark">Already have an account? Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const feedback = document.getElementById('passwordFeedback');

    function validatePassword(value) {
        const errors = [];
        if (value.length < 8) errors.push('At least 8 characters');
        if (!/[A-Z]/.test(value)) errors.push('One uppercase letter');
        if (!/[a-z]/.test(value)) errors.push('One lowercase letter');
        if (!/[0-9]/.test(value)) errors.push('One number');
        return errors;
    }

    function renderFeedback() {
        const errors = validatePassword(password.value);
        if (errors.length > 0) {
            feedback.style.display = 'block';
            feedback.textContent = 'Password must include: ' + errors.join(', ') + '.';
            password.classList.add('is-invalid');
            return false;
        }

        if (confirmPassword.value && password.value !== confirmPassword.value) {
            feedback.style.display = 'block';
            feedback.textContent = 'Passwords do not match.';
            password.classList.add('is-invalid');
            return false;
        }

        feedback.style.display = 'none';
        feedback.textContent = '';
        password.classList.remove('is-invalid');
        return true;
    }

    password.addEventListener('input', renderFeedback);
    confirmPassword.addEventListener('input', renderFeedback);
    form.addEventListener('submit', function (event) {
        if (!renderFeedback()) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
