<?php
/**
 * Organization Login.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];
$email  = post('email');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = post('password');

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter both your email address and password.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "organization"');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Invalid organization email or password.';
        } elseif ((int) $user['is_active'] !== 1) {
            $errors[] = 'Your account has been deactivated. Please contact support.';
        } else {
            $org = get_organization($pdo, $user['user_id']);
            login_user($user['user_id'], 'organization', $org ? $org['org_name'] : 'Organization');
            set_flash('success', 'Welcome back, ' . e(current_name()) . '!');
            redirect('/organization/dashboard.php');
        }
    }
}

$pageTitle = 'Organization Login';
include __DIR__ . '/../includes/header.php';
?>

<h1>Organization Portal Login</h1>
<p class="meta">Log in to publish internship offers and review student applicants.</p>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width:500px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Organization Email</label>
            <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="<?php echo BASE_URL; ?>/forgot-password.php" style="font-size: 0.9em;">Forgot Password?</a>
        </div>

        <div class="form-group" style="margin-top:18px;">
            <button type="submit" class="btn btn-amber">Log In to Portal</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/organization/register.php">Register New Organization</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
