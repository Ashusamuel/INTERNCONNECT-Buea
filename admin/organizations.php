<?php
/**
 * Admin Organization Management & Verification.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

// Handle verification toggle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'toggle_verification') {
    $orgId = (int) post('org_id');
    toggle_organization_verification($pdo, $orgId);
    set_flash('success', 'Organization verification status updated.');
    redirect('/admin/organizations.php');
}

$organizations = get_all_organizations($pdo);

$pageTitle = 'Manage Organizations';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Manage Partner Organizations</h1>
    <a class="btn btn-small" href="<?php echo BASE_URL; ?>/admin/dashboard.php">&larr; Back to Dashboard</a>
</div>

<?php if (empty($organizations)): ?>
    <div class="card" style="text-align:center; padding:36px;">
        <h3>No organizations registered yet</h3>
    </div>
<?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Organization Name</th>
                    <th>Email</th>
                    <th>Sector</th>
                    <th>Location</th>
                    <th>Listings</th>
                    <th>Verification Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($organizations as $o): ?>
                    <?php $isVerified = ((int) $o['is_verified'] === 1); ?>
                    <tr>
                        <td><strong><?php echo e($o['org_name']); ?></strong></td>
                        <td><?php echo e($o['email']); ?></td>
                        <td><?php echo e($o['sector'] ?: 'N/A'); ?></td>
                        <td><?php echo e($o['location'] ?: 'N/A'); ?></td>
                        <td><?php echo (int) $o['internship_count']; ?> posted</td>
                        <td>
                            <span class="status-pill <?php echo $isVerified ? 'pill-success' : 'pill-warning'; ?>">
                                <?php echo $isVerified ? 'Verified' : 'Pending'; ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="action" value="toggle_verification">
                                <input type="hidden" name="org_id" value="<?php echo (int) $o['org_id']; ?>">
                                <button type="submit" class="btn btn-small <?php echo $isVerified ? 'btn-outline' : 'btn-amber'; ?>">
                                    <?php echo $isVerified ? 'Revoke Verification' : 'Verify Organization'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
