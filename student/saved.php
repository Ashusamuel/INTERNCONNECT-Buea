<?php
/**
 * Student Saved / Bookmarked Internships.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('student');

$studentId = (int) $_SESSION['user_id'];
$student   = get_student($pdo, $studentId);

// Handle unsave POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'unsave') {
    $removeId = (int) post('internship_id');
    toggle_save_internship($pdo, $student['student_id'], $removeId);
    set_flash('success', 'Internship removed from your saved list.');
    redirect('/student/saved.php');
}

$savedList = get_saved_internships($pdo, $student['student_id']);

$pageTitle = 'Saved Internships';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>My Saved Internships</h1>
    <a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/internships.php">Browse All Internships</a>
</div>

<?php if (empty($savedList)): ?>
    <div class="card" style="text-align:center; padding:36px;">
        <h3>No saved internships</h3>
        <p class="form-hint">You haven't bookmarked any internships yet. Click the "Save Internship" button on any listing to store it here for later.</p>
        <p><a class="btn btn-amber" href="<?php echo BASE_URL; ?>/student/internships.php">Explore Opportunities</a></p>
    </div>
<?php else: ?>
    <div class="grid grid-2">
        <?php foreach ($savedList as $internship): ?>
            <div class="card">
                <h3>
                    <a href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $internship['internship_id']; ?>">
                        <?php echo e($internship['title']); ?>
                    </a>
                </h3>
                <p class="meta"><?php echo e($internship['org_name']); ?> &mdash; <?php echo e($internship['location']); ?></p>
                <p><?php echo e(short_text($internship['description'])); ?></p>
                
                <table class="table" style="margin:12px 0;">
                    <tr><th>Category</th><td><?php echo e($internship['category']); ?></td></tr>
                    <tr><th>Deadline</th><td><?php echo $internship['deadline'] ? e(date('d M Y', strtotime($internship['deadline']))) : 'Open'; ?></td></tr>
                </table>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                    <a class="btn btn-small btn-amber" href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $internship['internship_id']; ?>">
                        View Details
                    </a>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="unsave">
                        <input type="hidden" name="internship_id" value="<?php echo (int) $internship['internship_id']; ?>">
                        <button type="submit" class="btn btn-small btn-outline" onclick="return confirm('Remove this internship from your saved list?');">
                            Remove
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
