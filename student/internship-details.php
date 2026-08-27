<?php
/**
 * Internship details, eligibility check, application link, and bookmarking.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$internshipId = get('id');
$internship   = get_internship($pdo, $internshipId);

if (!$internship || (int) $internship['is_active'] !== 1) {
    set_flash('error', 'That internship is not available.');
    redirect('/student/internships.php');
}

$expired = is_expired($internship['deadline']);

$student     = null;
$eligibility = null;
$isSaved     = false;

if (current_role() === 'student') {
    $student     = get_student($pdo, $_SESSION['user_id']);
    $eligibility = check_student_eligibility($pdo, $student['student_id'], $internship['internship_id']);
    $isSaved     = is_internship_saved($pdo, $student['student_id'], $internship['internship_id']);

    // Handle bookmark toggle POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'toggle_save') {
        $savedState = toggle_save_internship($pdo, $student['student_id'], $internship['internship_id']);
        set_flash('success', $savedState ? 'Internship saved to your bookmarks.' : 'Internship removed from bookmarks.');
        redirect('/student/internship-details.php?id=' . (int) $internship['internship_id']);
    }
}

$pageTitle = $internship['title'];
include __DIR__ . '/../includes/header.php';
?>

<p><a href="<?php echo BASE_URL; ?>/student/internships.php">&larr; Back to internships</a></p>

<div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
    <div>
        <h1><?php echo e($internship['title']); ?></h1>
        <p class="meta"><?php echo e($internship['org_name']); ?> &mdash; <?php echo e($internship['location']); ?></p>
    </div>
    <?php if (current_role() === 'student'): ?>
        <form method="post" action="">
            <input type="hidden" name="action" value="toggle_save">
            <button type="submit" class="btn <?php echo $isSaved ? 'btn-outline' : ''; ?>">
                <?php echo $isSaved ? '&#9733; Saved in Bookmarks' : '&#9734; Save Internship'; ?>
            </button>
        </form>
    <?php endif; ?>
</div>

<?php if ($expired): ?>
    <div class="alert alert-error">The deadline for this internship has passed.</div>
<?php endif; ?>

<div class="grid grid-2">
    <div>
        <div class="card">
            <h3>Description</h3>
            <p><?php echo nl2br(e($internship['description'])); ?></p>
        </div>

        <div class="card">
            <h3>Requirements</h3>
            <p><?php echo $internship['requirements']
                ? nl2br(e($internship['requirements']))
                : '<span class="form-hint">No specific requirement listed.</span>'; ?></p>
        </div>

        <!-- Stage 5: Eligibility Check Card -->
        <?php if (current_role() === 'student'): ?>
            <div class="card">
                <h3>Eligibility Status</h3>
                <?php if ($eligibility['applied']): ?>
                    <div class="alert alert-info">You have already submitted an application for this internship position.</div>
                <?php elseif ($eligibility['eligible']): ?>
                    <div class="alert alert-success">
                        <strong>Great news! You meet all eligibility requirements for this internship.</strong>
                    </div>
                    <p style="margin-top:10px;">
                        <a class="btn btn-amber" href="<?php echo BASE_URL; ?>/student/apply.php?id=<?php echo (int) $internship['internship_id']; ?>">
                            Apply for this Internship &rarr;
                        </a>
                    </p>
                <?php else: ?>
                    <div class="alert alert-error">
                        <strong>You do not meet all criteria for this internship position:</strong>
                        <ul style="margin-top:8px; margin-left:18px;">
                            <?php foreach ($eligibility['reasons'] as $reason): ?>
                                <li><?php echo e($reason); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <p class="form-hint">
                        <a href="<?php echo BASE_URL; ?>/student/edit-profile.php">Update your profile or upload a CV</a> to resolve eligibility requirements.
                    </p>
                <?php endif; ?>

                <table class="table" style="margin-top:12px;">
                    <tr>
                        <th>Profile Completion</th>
                        <td>
                            <?php echo $eligibility['completion']; ?>%
                            (Min 50% required &mdash; <?php echo $eligibility['completion_ok'] ? '<span class="text-success">OK</span>' : '<span class="text-danger">Needs work</span>'; ?>)
                        </td>
                    </tr>
                    <tr>
                        <th>CV Uploaded</th>
                        <td><?php echo $eligibility['cv_ok'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No CV uploaded</span>'; ?></td>
                    </tr>
                    <tr>
                        <th>Academic Level</th>
                        <td>
                            Required: <?php echo e($internship['required_level'] ?: 'Any'); ?> | Your Level: <?php echo e($student['level'] ?: 'Not set'); ?>
                            (<?php echo $eligibility['level_ok'] ? '<span class="text-success">Eligible</span>' : '<span class="text-danger">Level too low</span>'; ?>)
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card">
            <h3>Summary</h3>
            <table class="table">
                <tr><th>Category</th><td><?php echo e($internship['category']); ?></td></tr>
                <tr><th>Location</th><td><?php echo e($internship['location']); ?></td></tr>
                <tr><th>Duration</th><td><?php echo e($internship['duration'] ?: 'not stated'); ?></td></tr>
                <tr><th>Minimum level</th><td><?php echo e($internship['required_level'] ?: 'any'); ?></td></tr>
                <tr><th>Positions</th><td><?php echo (int) $internship['positions']; ?></td></tr>
                <tr>
                    <th>Deadline</th>
                    <td><?php echo $internship['deadline']
                        ? e(date('d M Y', strtotime($internship['deadline'])))
                        : 'open'; ?></td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h3>About <?php echo e($internship['org_name']); ?></h3>
            <p><?php echo e($internship['org_description'] ?: 'No description provided.'); ?></p>
            <table class="table">
                <tr><th>Sector</th><td><?php echo e($internship['sector'] ?: 'not stated'); ?></td></tr>
                <tr><th>Based in</th><td><?php echo e($internship['org_location'] ?: 'not stated'); ?></td></tr>
                <?php if (!empty($internship['website'])): ?>
                    <tr><th>Website</th><td><a href="<?php echo e($internship['website']); ?>" target="_blank" rel="noopener"><?php echo e($internship['website']); ?></a></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <?php if (!is_logged_in()): ?>
            <div class="card">
                <h3>Interested?</h3>
                <p class="form-hint">Log in as a student to check your eligibility and apply for this internship.</p>
                <p><a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/login.php">Student login</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

