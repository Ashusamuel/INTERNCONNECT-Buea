<?php
/**
 * Organization Registration.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];
$orgName     = post('org_name');
$email       = post('email');
$password    = post('password');
$confirmPass = post('confirm_password');
$sector      = post('sector');
$location    = post('location');
$phone       = post('phone');
$website     = post('website');
$description = post('description');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($orgName)) {
        $errors[] = 'Organization name is required.';
    }
    if (empty($email) || !is_valid_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if ($password !== $confirmPass) {
        $errors[] = 'Password and confirmation do not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email address already exists.';
        }
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmtUser = $pdo->prepare('INSERT INTO users (email, password, role) VALUES (?, ?, "organization")');
            $stmtUser->execute([$email, $passHash]);
            $userId = (int) $pdo->lastInsertId();

            $stmtOrg = $pdo->prepare(
                'INSERT INTO organizations (user_id, org_name, sector, location, phone, website, description, is_verified)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 0)'
            );
            $stmtOrg->execute([$userId, $orgName, $sector, $location, $phone, $website, $description]);

            $pdo->commit();

            login_user($userId, 'organization', $orgName);
            set_flash('success', 'Organization account created successfully! Welcome to InternConnect.');
            redirect('/organization/dashboard.php');
        } catch (Exception $ex) {
            $pdo->rollBack();
            $errors[] = 'Database error during registration: ' . $ex->getMessage();
        }
    }
}

$pageTitle = 'Organization Registration';
include __DIR__ . '/../includes/header.php';
?>

<h1>Register Organization Account</h1>
<p class="meta">Publish internship offers and connect with talented students in Buea.</p>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width:700px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="org_name">Organization / Company Name *</label>
            <input type="text" id="org_name" name="org_name" value="<?php echo e($orgName); ?>" required>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="email">Work Email Address *</label>
                <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo e($phone); ?>" placeholder="e.g. 677100200">
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                <span class="form-hint">At least 6 characters.</span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="sector">Industry / Sector</label>
                <input type="text" id="sector" name="sector" value="<?php echo e($sector); ?>" placeholder="e.g. Technology, Finance, Health">
            </div>
            <div class="form-group">
                <label for="location">Location / Office Address</label>
                <input type="text" id="location" name="location" value="<?php echo e($location); ?>" placeholder="e.g. Molyko, Buea">
            </div>
        </div>

        <div class="form-group">
            <label for="website">Company Website</label>
            <input type="url" id="website" name="website" value="<?php echo e($website); ?>" placeholder="https://example.cm">
        </div>

        <div class="form-group">
            <label for="description">Company Overview / Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Briefly describe what your organization does..."><?php echo e($description); ?></textarea>
        </div>

        <div class="form-group" style="margin-top:18px;">
            <button type="submit" class="btn btn-amber">Create Organization Account</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/login.php">Already registered? Log in</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
