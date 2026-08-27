<?php
/**
 * Student dashboard.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('student');

$student      = get_student($pdo, $_SESSION['user_id']);
$skillNames   = student_skill_names($pdo, $student['student_id']);
$completion   = profile_completion($student, $skillNames);
$applications = get_student_applications($pdo, $student['student_id']);
$savedList    = get_saved_internships($pdo, $student['student_id']);
$recommended  = get_recommended_internships($pdo, $student['student_id']);

$pageTitle = 'Student Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<h1>Welcome, <?php echo e($student['full_name']); ?></h1>

<div class="grid grid-3">
    <div class="stat">
        <div class="stat-value"><?php echo (int) $completion; ?>%</div>
        <div class="stat-label">Profile Completed</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo count($applications); ?></div>
        <div class="stat-label">Applications Submitted</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?php echo count($savedList); ?></div>
        <div class="stat-label">Saved Internships</div>
    </div>
</div>

<?php if ($completion < 100): ?>
    <div class="alert alert-info" style="margin-top:18px;">
        Complete your profile (university, programme, level, skills and CV) so organizations can
        evaluate your applications. <a href="<?php echo BASE_URL; ?>/student/edit-profile.php">Finish it now</a>.
    </div>
<?php endif; ?>

<div class="grid grid-2" style="margin-top:18px;">
    <div class="card">
        <h3>Recent Applications</h3>
        <?php if (empty($applications)): ?>
            <p class="form-hint">You haven't applied for any internships yet.</p>
            <p><a class="btn btn-small btn-amber" href="<?php echo BASE_URL; ?>/student/internships.php">Browse Opportunities</a></p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Internship</th>
                        <th>Organization</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($applications, 0, 5) as $app):
                        $status = $app['status'];
                        $badgeClass = 'badge ' . (
                            $status === 'accepted' ? 'badge-success' :
                            ($status === 'rejected' ? 'badge-error' :
                            ($status === 'reviewed' ? 'badge-warning' : 'badge-info'))
                        );
                    ?>
                        <tr>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $app['internship_id']; ?>">
                                    <?php echo e($app['title']); ?>
                                </a>
                            </td>
                            <td><?php echo e($app['org_name']); ?></td>
                            <td>
                                <span class="<?php echo $badgeClass; ?>" style="text-transform:capitalize;">
                                    <?php echo e(ucfirst($status)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top:12px;">
                <a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/applications.php">View All Applications &rarr;</a>
            </p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Quick Navigation</h3>
        <ul style="line-height:2;">
            <li><a href="<?php echo BASE_URL; ?>/student/internships.php">Find Internships in Buea</a></li>
            <li><a href="<?php echo BASE_URL; ?>/student/applications.php">Track Submitted Applications (<?php echo count($applications); ?>)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/student/saved.php">View Saved Bookmarks (<?php echo count($savedList); ?>)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/student/edit-profile.php">Update Skills & CV Upload</a></li>
        </ul>
    </div>
</div>

<?php if (!empty($recommended)): ?>
    <h2 style="margin-top:32px;">Recommended for You</h2>
    <p class="meta" style="margin-top:-8px; margin-bottom:18px;">Based on your matching skills profile.</p>
    <div class="grid grid-2">
        <?php 
        // Show up to 4 recommendations
        foreach (array_slice($recommended, 0, 4) as $internship): 
        ?>
            <?php include __DIR__ . '/../includes/internship-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

