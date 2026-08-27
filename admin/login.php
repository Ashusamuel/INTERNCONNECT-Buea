<?php
/**
 * Administrator Login Page.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    if (current_role() === 'admin') {
        redirect('/admin/dashboard.php');
    } else {
        redirect('/index.php');
    }
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = post('email');
    $password = post('password');

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter administrator email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "admin"');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Invalid administrator credentials.';
        } elseif ((int) $user['is_active'] !== 1) {
            $errors[] = 'Admin account is deactivated.';
        } else {
            login_user($user['user_id'], 'admin', 'System Administrator');
            set_flash('success', 'Logged in as System Administrator.');
            redirect('/admin/dashboard.php');
        }
    }
}

$pageTitle = 'Admin Login';
include __DIR__ . '/../includes/header.php';
?>

<h1>System Administrator Login</h1>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width:450px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group" style="margin-top:18px;">
            <button type="submit" class="btn btn-amber">Log In to Admin Dashboard</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
