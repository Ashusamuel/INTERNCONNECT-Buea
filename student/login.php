<?php
/**
 * Page 3 - Student login.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(post('email'));
    $password = post('password');

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter your email and password.';
    }

    if (!$errors) {
        $sql = 'SELECT u.user_id, u.password, u.is_active, s.full_name
                FROM users u
                JOIN students s ON s.user_id = u.user_id
                WHERE u.email = ? AND u.role = "student"';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // The same message is used for a wrong email and a wrong password.
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Incorrect email or password.';
        } elseif ((int) $user['is_active'] !== 1) {
            $errors[] = 'This account has been disabled. Please contact the administrator.';
        } else {
            login_user($user['user_id'], 'student', $user['full_name']);
            set_flash('success', 'Welcome back, ' . $user['full_name'] . '.');
            redirect('/student/dashboard.php');
        }
    }
}

$pageTitle = 'Student login';
include __DIR__ . '/../includes/header.php';
?>

<h1>Student login</h1>
<p class="form-hint">No account yet? <a href="<?php echo BASE_URL; ?>/student/register.php">Register here</a>.</p>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <div><?php echo e($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width:440px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="<?php echo BASE_URL; ?>/forgot-password.php" style="font-size: 0.9em;">Forgot Password?</a>
        </div>

        <button type="submit" class="btn">Log in</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
