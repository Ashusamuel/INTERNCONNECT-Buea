<?php
/**
 * Administrator Dashboard Overview.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$stats = get_admin_stats($pdo);

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<h1>System Administrator Dashboard</h1>
<p class="meta">Platform management and metrics overview for InternConnect Buea.</p>

<div class="grid grid-3">
    <div class="stat">
        <div class="stat-value"><?php echo $stats['total_users']; ?></div>
        <div class="stat-label">Total Accounts</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo $stats['total_students']; ?></div>
        <div class="stat-label">Registered Students</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo $stats['total_organizations']; ?></div>
        <div class="stat-label">Organizations (<?php echo $stats['unverified_orgs']; ?> unverified)</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo $stats['total_internships']; ?></div>
        <div class="stat-label">Total Internships</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo $stats['total_applications']; ?></div>
        <div class="stat-label">Applications Submitted</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo $stats['unverified_orgs']; ?></div>
        <div class="stat-label">Pending Org Verifications</div>
    </div>
</div>

<div class="grid grid-3" style="margin-top:24px;">
    <div class="card">
        <h3>Organizations System</h3>
        <p>Verify newly registered companies, review profiles, and manage active status.</p>
        <p><a class="btn btn-amber" href="<?php echo BASE_URL; ?>/admin/organizations.php">Manage Organizations &rarr;</a></p>
    </div>

    <div class="card">
        <h3>User Management</h3>
        <p>View all student, organization, and administrator user accounts. Deactivate or reactivate users.</p>
        <p><a class="btn btn-amber" href="<?php echo BASE_URL; ?>/admin/users.php">Manage Users &rarr;</a></p>
    </div>

    <div class="card">
        <h3>Internship Listings</h3>
        <p>Monitor all active and closed internship positions posted across all partner organizations.</p>
        <p><a class="btn btn-amber" href="<?php echo BASE_URL; ?>/admin/internships.php">Manage Internships &rarr;</a></p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
