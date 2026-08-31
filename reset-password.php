<?php
/**
 * Reset Password.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$validToken = false;
$user = null;

if (empty($token) || empty($email)) {
    $errors[] = 'Invalid or missing reset token.';
} else {
    // Validate token and email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && !empty($user['reset_token']) && $user['reset_expires'] > date('Y-m-d H:i:s')) {
        if (password_verify($token, $user['reset_token'])) {
            $validToken = true;
        } else {
            $errors[] = 'Invalid reset token.';
        }
    } else {
        $errors[] = 'The reset link has expired or is invalid.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = post('password');
    $password_confirm = post('password_confirm');

    if (empty($password)) {
        $errors[] = 'Please enter a new password.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE user_id = ?');
        $update->execute([$hash, $user['user_id']]);

        set_flash('success', 'Your password has been successfully reset. You can now log in.');
        redirect('/login.php');
    }
}

$pageTitle = 'Reset Password';
include __DIR__ . '/includes/header.php';
?>

<h1>Reset Your Password</h1>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!$validToken): ?>
    <div style="margin-top:20px;">
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/forgot-password.php">Request New Reset Link</a>
    </div>
<?php else: ?>
    <div class="card" style="max-width:500px; margin:0 auto;">
        <form method="post" action="">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required autofocus>
                <small class="form-hint">Must be at least 8 characters.</small>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm New Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>

            <div class="form-group" style="margin-top:18px;">
                <button type="submit" class="btn btn-amber">Reset Password</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
