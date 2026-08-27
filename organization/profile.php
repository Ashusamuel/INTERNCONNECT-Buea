<?php
/**
 * View Organization Profile.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$pageTitle = $org['org_name'] . ' - Profile';
include __DIR__ . '/../includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
    <h1><?php echo e($org['org_name']); ?></h1>
    <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/organization/edit-profile.php">Edit Profile</a>
</div>

<div class="grid grid-2">
    <div class="card">
        <h3>Company Overview</h3>
        <p><?php echo nl2br(e($org['description'] ?: 'No description provided. Click Edit Profile to add an overview of your organization.')); ?></p>

        <table class="table" style="margin-top:18px;">
            <tr><th>Verification Status</th>
                <td>
                    <?php if ((int) $org['is_verified'] === 1): ?>
                        <span class="btn btn-small alert-success" style="padding:2px 8px; cursor:default;">Verified Partner</span>
                    <?php else: ?>
                        <span class="btn btn-small alert-warning" style="padding:2px 8px; cursor:default;">Pending Verification</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr><th>Account Email</th><td><?php echo e($org['email']); ?></td></tr>
            <tr><th>Sector / Industry</th><td><?php echo e($org['sector'] ?: 'Not set'); ?></td></tr>
            <tr><th>Office Location</th><td><?php echo e($org['location'] ?: 'Not set'); ?></td></tr>
            <tr><th>Phone Number</th><td><?php echo e($org['phone'] ?: 'Not set'); ?></td></tr>
            <tr><th>Website</th><td><?php echo $org['website'] ? '<a href="' . e($org['website']) . '" target="_blank" rel="noopener">' . e($org['website']) . '</a>' : 'Not set'; ?></td></tr>
            <tr><th>Member Since</th><td><?php echo e(date('d M Y', strtotime($org['created_at']))); ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h3>Portal Actions</h3>
        <p>Manage your account settings and internship listings:</p>
        <p><a class="btn btn-small btn-amber" href="<?php echo BASE_URL; ?>/organization/post-internship.php">+ Post New Internship</a></p>
        <p><a class="btn btn-small" href="<?php echo BASE_URL; ?>/organization/internships.php">View Posted Internships</a></p>
        <p><a class="btn btn-small btn-outline" href="<?php echo BASE_URL; ?>/organization/edit-profile.php">Edit Profile Information</a></p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
