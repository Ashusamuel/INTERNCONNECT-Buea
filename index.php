<?php
/**
 * Page 1 - Landing page.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Find your internship in Buea</h1>
    <p>
        InternConnect Buea brings students and local organizations together in one place:
        discover opportunities, check whether you qualify, apply online and follow the
        progress of every application.
    </p>
    <p>
        <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/student/internships.php">Browse internships</a>
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/student/register.php">Create a student account</a>
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/login.php">Log in</a>
    </p>
</section>

<div class="grid grid-3">
    <div class="card">
        <h3>For students</h3>
        <p>Build a profile, upload your CV, check eligibility, bookmark listings, and track your applications.</p>
    </div>
    <div class="card">
        <h3>For organizations</h3>
        <p>Publish internship offers, describe requirements, and manage student applicant status seamlessly.</p>
    </div>
    <div class="card">
        <h3>Admin & Verification</h3>
        <p>Comprehensive system administration, organization verification, and platform usage metrics.</p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
