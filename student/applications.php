<?php
/**
 * Student Submitted Applications History.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('student');

$student      = get_student($pdo, $_SESSION['user_id']);
$applications = get_student_applications($pdo, $student['student_id']);

$pageTitle = 'My Applications';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>My Submitted Applications</h1>
    <a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/internships.php">Browse More Internships</a>
</div>

<?php if (empty($applications)): ?>
    <div class="card" style="text-align:center; padding:36px;">
        <h3>No applications yet</h3>
        <p class="form-hint">You haven't submitted any internship applications so far.</p>
        <p><a class="btn btn-amber" href="<?php echo BASE_URL; ?>/student/internships.php">Discover Internships</a></p>
    </div>
<?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Internship</th>
                    <th>Organization</th>
                    <th>Category</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($applications as $app): ?>
                        <?php
                        $pillClass = 'status-pill';
                        if ($app['status'] === 'accepted') $pillClass .= ' pill-success';
                        elseif ($app['status'] === 'rejected') $pillClass .= ' pill-error';
                        elseif ($app['status'] === 'reviewed') $pillClass .= ' pill-warning';
                        else $pillClass .= ' pill-info';
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $app['internship_id']; ?>">
                                        <?php echo e($app['title']); ?>
                                    </a>
                                </strong>
                            </td>
                            <td><?php echo e($app['org_name']); ?></td>
                            <td><?php echo e($app['category']); ?></td>
                            <td><?php echo e(date('d M Y, H:i', strtotime($app['applied_at']))); ?></td>
                            <td>
                                <span class="<?php echo $pillClass; ?>">
                                    <?php echo e(ucfirst($app['status'])); ?>
                                </span>
                            </td>
                        </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
