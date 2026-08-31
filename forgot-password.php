<?php
/**
 * Forgot Password.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];
$email = '';
$success = false;
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim(post('email')));

    if (empty($email)) {
        $errors[] = 'Please enter your email address.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            $update = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?');
            $update->execute([$tokenHash, $expires, $user['user_id']]);

            // Link for local dev (Normally sent via email)
            $resetLink = BASE_URL . '/reset-password.php?token=' . urlencode($token) . '&email=' . urlencode($email);
            $success = true;
        } else {
            // For security, don't reveal if the email exists or not, but display a generic success message
            // or in this case for dev we can just do the same.
            $success = true; 
        }
    }
}

$pageTitle = 'Forgot Password';
include __DIR__ . '/includes/header.php';
?>

<h1>Forgot Password</h1>
<p class="meta">Enter your email address to receive a password reset link.</p>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <p>If an account with that email exists, a password reset link has been generated.</p>
        <?php if ($resetLink): ?>
            <hr style="margin: 10px 0; border-color: rgba(255,255,255,0.2);">
            <p><strong>Dev Mode Notice:</strong> Click the link below to reset your password.</p>
            <a href="<?php echo htmlspecialchars($resetLink); ?>" style="color: #155724; text-decoration: underline; word-break: break-all;"><?php echo htmlspecialchars($resetLink); ?></a>
        <?php endif; ?>
    </div>
    <div style="margin-top:20px;">
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/login.php">Back to Login</a>
    </div>
<?php else: ?>
    <div class="card" style="max-width:500px; margin:0 auto;">
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Account Email</label>
                <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required autofocus>
            </div>

            <div class="form-group" style="margin-top:18px;">
                <button type="submit" class="btn btn-amber">Request Reset Link</button>
                <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/login.php">Back to Login</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
