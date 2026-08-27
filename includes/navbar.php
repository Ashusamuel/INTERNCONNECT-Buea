<?php
/**
 * Reusable navigation bar with full role-based links.
 */
$role = current_role();
?>
<header class="navbar">
    <div class="navbar-inner container">
        <a class="brand" href="<?php echo BASE_URL; ?>/index.php">
            Intern<span>Connect</span> Buea
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">&#9776;</button>

        <nav class="nav-links" id="navLinks">
            <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
            <a href="<?php echo BASE_URL; ?>/student/internships.php">Internships</a>

            <?php if ($role === 'student'): ?>
                <a href="<?php echo BASE_URL; ?>/student/dashboard.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/student/applications.php">Applications</a>
                <a href="<?php echo BASE_URL; ?>/student/saved.php">Saved</a>
                <a href="<?php echo BASE_URL; ?>/student/profile.php">My Profile</a>
            <?php elseif ($role === 'organization'): ?>
                <a href="<?php echo BASE_URL; ?>/organization/dashboard.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/organization/internships.php">My Listings</a>
                <a href="<?php echo BASE_URL; ?>/organization/applications.php">Applicants</a>
                <a href="<?php echo BASE_URL; ?>/organization/profile.php">Org Profile</a>
            <?php elseif ($role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/organizations.php">Organizations</a>
                <a href="<?php echo BASE_URL; ?>/admin/users.php">Users</a>
                <a href="<?php echo BASE_URL; ?>/admin/internships.php">All Internships</a>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <span class="nav-user">Hello, <?php echo e(current_name()); ?></span>
                <a class="btn btn-small" href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-small btn-outline" href="<?php echo BASE_URL; ?>/login.php">Login</a>
                <a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

