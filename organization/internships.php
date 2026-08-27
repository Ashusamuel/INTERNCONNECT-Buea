<?php
/**
 * Organization Internships Management List.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

// Handle status toggle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'toggle_status') {
    $internshipId = (int) post('internship_id');
    toggle_internship_status($pdo, $internshipId, $org['org_id']);
    set_flash('success', 'Internship status updated successfully.');
    redirect('/organization/internships.php');
}

$internships = get_org_internships($pdo, $org['org_id']);

$pageTitle = 'Manage Internships';
include __DIR__ . '/../includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
    <h1>My Posted Internships</h1>
    <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/organization/post-internship.php">+ Post New Internship</a>
</div>

<?php if (empty($internships)): ?>
    <div class="card" style="text-align:center; padding:36px;">
        <h3>No internship listings posted yet</h3>
        <p class="form-hint">Publish your first internship offer to start receiving applications from students in Buea.</p>
        <p><a class="btn btn-amber" href="<?php echo BASE_URL; ?>/organization/post-internship.php">+ Post Internship Now</a></p>
    </div>
<?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Required Level</th>
                    <th>Deadline</th>
                    <th>Applicants</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($internships as $i): ?>
                    <?php $isActive = ((int) $i['is_active'] === 1); ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $i['internship_id']; ?>" target="_blank">
                                    <?php echo e($i['title']); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo e($i['category']); ?></td>
                        <td><?php echo e($i['required_level'] ?: 'Any'); ?></td>
                        <td><?php echo $i['deadline'] ? e(date('d M Y', strtotime($i['deadline']))) : 'Open'; ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/organization/applications.php?internship_id=<?php echo (int) $i['internship_id']; ?>">
                                <strong><?php echo (int) $i['applicant_count']; ?></strong> applicants
                            </a>
                        </td>
                        <td>
                            <span class="btn btn-small <?php echo $isActive ? 'alert-success' : 'alert-error'; ?>" style="cursor:default; padding:2px 8px;">
                                <?php echo $isActive ? 'Active' : 'Closed'; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a class="btn btn-small" href="<?php echo BASE_URL; ?>/organization/edit-internship.php?id=<?php echo (int) $i['internship_id']; ?>">
                                    Edit
                                </a>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="internship_id" value="<?php echo (int) $i['internship_id']; ?>">
                                    <button type="submit" class="btn btn-small btn-outline">
                                        <?php echo $isActive ? 'Close Listing' : 'Activate'; ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
