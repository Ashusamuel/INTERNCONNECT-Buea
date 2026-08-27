<?php
/**
 * Login chooser for Students, Organizations, and System Administrators.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>

<h1>Log in to InternConnect Buea</h1>
<p class="meta">Select your portal below to sign in to your account.</p>

<div class="grid grid-2">
    <div class="card">
        <h3>Student Portal</h3>
        <p>Search internships, check your eligibility, bookmark positions, and follow your applications.</p>
        <p>
            <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/student/login.php">Student Login</a>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/student/register.php">Register</a>
        </p>
    </div>

    <div class="card">
        <h3>Organization Portal</h3>
        <p>Publish internship offers, manage listings, and review student applicants.</p>
        <p>
            <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/organization/login.php">Organization Login</a>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/organization/register.php">Register</a>
        </p>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
