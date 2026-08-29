<?php
/**
 * Page 1 - Landing page.*/
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

<section class="hero" style="text-align: center; padding: 60px 20px;">
    <h1 style="font-size: 2.5rem; margin-bottom: 20px;">Welcome to InternConnect Buea</h1>
    <p style="font-size: 1.1rem; max-width: 800px; margin: 0 auto 30px auto; line-height: 1.6;">
        Empowering the next generation of professionals by bridging the gap between ambitious students and forward-thinking organizations in Buea. Discover tailored internship opportunities, build your career foundation, and shape your future with us.
    </p>
    <p>
        <a class="btn btn-amber" style="margin: 0 10px;" href="<?php echo BASE_URL; ?>/student/internships.php">Explore Opportunities</a>
        <a class="btn btn-outline" style="margin: 0 10px;" href="<?php echo BASE_URL; ?>/student/register.php">Join as a Student</a>
        <a class="btn btn-outline" style="margin: 0 10px;" href="<?php echo BASE_URL; ?>/login.php">Sign In</a>
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
