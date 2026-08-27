<?php
/**
 * Admin User Management.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

// Handle user active toggle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'toggle_active') {
    $targetUserId = (int) post('user_id');
    if ($targetUserId === (int) $_SESSION['user_id']) {
        set_flash('error', 'You cannot deactivate your own active admin account.');
    } else {
        toggle_user_active($pdo, $targetUserId);
        set_flash('success', 'User account status updated.');
    }
    redirect('/admin/users.php');
}

$users = get_all_users($pdo);

$pageTitle = 'Manage Users';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>User Account Management</h1>
    <a class="btn btn-small" href="<?php echo BASE_URL; ?>/admin/dashboard.php">&larr; Back to Dashboard</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Display Name</th>
                <th>Email Address</th>
                <th>Role</th>
                <th>Registered Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <?php $isActive = ((int) $u['is_active'] === 1); ?>
                <tr>
                    <td>#<?php echo (int) $u['user_id']; ?></td>
                    <td><strong><?php echo e($u['display_name']); ?></strong></td>
                    <td><?php echo e($u['email']); ?></td>
                    <td>
                        <span class="status-pill pill-neutral" style="text-transform:capitalize;">
                            <?php echo e($u['role']); ?>
                        </span>
                    </td>
                    <td><?php echo e(date('d M Y', strtotime($u['created_at']))); ?></td>
                    <td>
                        <span class="status-pill <?php echo $isActive ? 'pill-success' : 'pill-error'; ?>">
                            <?php echo $isActive ? 'Active' : 'Deactivated'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ((int) $u['user_id'] !== (int) $_SESSION['user_id']): ?>
                            <form method="post" action="">
                                <input type="hidden" name="action" value="toggle_active">
                                <input type="hidden" name="user_id" value="<?php echo (int) $u['user_id']; ?>">
                                <button type="submit" class="btn btn-small <?php echo $isActive ? 'btn-outline' : 'btn-amber'; ?>">
                                    <?php echo $isActive ? 'Deactivate' : 'Reactivate'; ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="form-hint">(Current Admin)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
