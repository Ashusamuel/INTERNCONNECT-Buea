<?php
/**
 * Organization Dashboard.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$internships  = get_org_internships($pdo, $org['org_id']);
$applications = get_org_applications($pdo, $org['org_id']);

$activeCount  = 0;
foreach ($internships as $i) {
    if ((int) $i['is_active'] === 1) {
        $activeCount++;
    }
}

$pendingAppsCount = 0;
foreach ($applications as $a) {
    if ($a['status'] === 'pending') {
        $pendingAppsCount++;
    }
}

$pageTitle = 'Organization Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Welcome, <?php echo e($org['org_name']); ?></h1>
        <p class="meta">
            Sector: <?php echo e($org['sector'] ?: 'Not specified'); ?> &mdash; <?php echo e($org['location'] ?: 'Buea'); ?>
            | Status: 
            <?php if ((int) $org['is_verified'] === 1): ?>
                <span class="status-pill pill-success">Verified Partner</span>
            <?php else: ?>
                <span class="status-pill pill-warning">Pending Verification</span>
            <?php endif; ?>
        </p>
    </div>
    <div>
        <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/organization/post-internship.php">+ Post New Internship</a>
    </div>
</div>

<div class="grid grid-3">
    <div class="stat">
        <div class="stat-value"><?php echo count($internships); ?></div>
        <div class="stat-label">Total Listings Posted</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo $activeCount; ?></div>
        <div class="stat-label">Active Postings</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo count($applications); ?></div>
        <div class="stat-label">Applications Received</div>
    </div>
</div>

<div class="grid grid-2" style="margin-top:18px;">
    <div class="card">
        <h3>Recent Applications</h3>
        <?php if (empty($applications)): ?>
            <p class="form-hint">No applications received yet for your posted internships.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Internship</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($applications, 0, 5) as $app): ?>
                        <tr>
                            <td><strong><?php echo e($app['full_name']); ?></strong></td>
                            <td><?php echo e($app['title']); ?></td>
                            <td><?php echo e(date('d M Y', strtotime($app['applied_at']))); ?></td>
                            <td>
                                <span class="form-hint" style="font-weight:bold; text-transform:capitalize;">
                                    <?php echo e($app['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-small" href="<?php echo BASE_URL; ?>/organization/application-details.php?id=<?php echo (int) $app['application_id']; ?>">
                                    Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top:12px;">
                <a class="btn btn-small btn-outline" href="<?php echo BASE_URL; ?>/organization/applications.php">View All Applications (<?php echo count($applications); ?>) &rarr;</a>
            </p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Organization Management</h3>
        <ul style="line-height:2;">
            <li><a href="<?php echo BASE_URL; ?>/organization/post-internship.php">Post a New Internship Position</a></li>
            <li><a href="<?php echo BASE_URL; ?>/organization/internships.php">Manage Posted Internships (<?php echo count($internships); ?>)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/organization/applications.php">Review Student Applicants (<?php echo $pendingAppsCount; ?> pending)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/organization/profile.php">View Organization Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>/organization/edit-profile.php">Edit Profile & Contact Details</a></li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
