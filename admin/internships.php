<?php
/**
 * Admin Internship Overview Management.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$internships = get_all_internships_admin($pdo);

$pageTitle = 'Manage All Internships';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>All Platform Internships</h1>
    <a class="btn btn-small" href="<?php echo BASE_URL; ?>/admin/dashboard.php">&larr; Back to Dashboard</a>
</div>

<?php if (empty($internships)): ?>
    <div class="card" style="text-align:center; padding:36px;">
        <h3>No internships posted on the platform</h3>
    </div>
<?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Organization</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Applicants</th>
                    <th>Status</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($internships as $i): ?>
                    <?php $isActive = ((int) $i['is_active'] === 1); ?>
                    <tr>
                        <td><strong><?php echo e($i['title']); ?></strong></td>
                        <td><?php echo e($i['org_name']); ?></td>
                        <td><?php echo e($i['category']); ?></td>
                        <td><?php echo e($i['location']); ?></td>
                        <td><strong><?php echo (int) $i['applicant_count']; ?></strong> applicants</td>
                        <td>
                            <span class="status-pill <?php echo $isActive ? 'pill-success' : 'pill-error'; ?>">
                                <?php echo $isActive ? 'Active' : 'Closed'; ?>
                            </span>
                        </td>
                        <td>
                            <a class="btn btn-small btn-amber" href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $i['internship_id']; ?>" target="_blank">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
