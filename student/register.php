<?php
/**
 * Page 2 - Student registration.
 * Creates one row in "users" (role = student) and one row in "students".
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// A logged in user has nothing to do here.
if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];
$fullName = '';
$email    = '';
$phone    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName  = post('full_name');
    $email     = strtolower(post('email'));
    $phone     = post('phone');
    $password  = post('password');
    $confirm   = post('confirm_password');

    // ---- validation -------------------------------------------------
    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    } elseif (strlen($fullName) < 3) {
        $errors[] = 'Full name must be at least 3 characters.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!is_valid_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($phone !== '' && !preg_match('/^[0-9 +\-]{6,20}$/', $phone)) {
        $errors[] = 'Phone number may only contain digits, spaces, + and -.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'The two passwords do not match.';
    }

    // ---- email must be unique ---------------------------------------
    if (!$errors) {
        $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'That email address is already registered.';
        }
    }

    // ---- save --------------------------------------------------------
    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $insertUser = $pdo->prepare(
                'INSERT INTO users (email, password, role) VALUES (?, ?, "student")'
            );
            $insertUser->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
            $userId = (int) $pdo->lastInsertId();

            $insertStudent = $pdo->prepare(
                'INSERT INTO students (user_id, full_name, phone) VALUES (?, ?, ?)'
            );
            $insertStudent->execute([$userId, $fullName, ($phone === '' ? null : $phone)]);

            $pdo->commit();

            set_flash('success', 'Your account was created. You can now log in.');
            redirect('/student/login.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Could not create the account. Please try again.';
        }
    }
}

$pageTitle = 'Student registration';
include __DIR__ . '/../includes/header.php';
?>

<h1>Create a student account</h1>
<p class="form-hint">Already registered? <a href="<?php echo BASE_URL; ?>/student/login.php">Log in here</a>.</p>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <div><?php echo e($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width:520px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="full_name">Full name *</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo e($fullName); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone number</label>
            <input type="text" id="phone" name="phone" value="<?php echo e($phone); ?>">
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>
            <p class="form-hint">At least 6 characters.</p>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password *</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn">Register</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
