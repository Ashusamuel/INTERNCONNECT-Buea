<?php
/**
 * Page 13 - Student profile (read only).
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('student');

$student    = get_student($pdo, $_SESSION['user_id']);
$skillNames = student_skill_names($pdo, $student['student_id']);

$pageTitle = 'My profile';
include __DIR__ . '/../includes/header.php';
?>

<h1>My profile</h1>
<p><a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/edit-profile.php">Edit profile</a></p>

<div class="card">
    <h3><?php echo e($student['full_name']); ?></h3>
    <table class="table">
        <tr><th>Email</th><td><?php echo e($student['email']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo e($student['phone'] ?: 'not set'); ?></td></tr>
        <tr><th>University / school</th><td><?php echo e($student['university'] ?: 'not set'); ?></td></tr>
        <tr><th>Programme</th><td><?php echo e($student['programme'] ?: 'not set'); ?></td></tr>
        <tr><th>Level</th><td><?php echo e($student['level'] ?: 'not set'); ?></td></tr>
        <tr><th>Location</th><td><?php echo e($student['location'] ?: 'not set'); ?></td></tr>
    </table>
</div>

<div class="card">
    <h3>About me</h3>
    <p><?php echo $student['bio'] ? nl2br(e($student['bio'])) : '<span class="form-hint">No description yet.</span>'; ?></p>
</div>

<div class="card">
    <h3>Skills</h3>
    <?php if ($skillNames): ?>
        <p>
            <?php foreach ($skillNames as $skill): ?>
                <span class="badge badge-success"><?php echo e($skill); ?></span>
            <?php endforeach; ?>
        </p>
    <?php else: ?>
        <p class="form-hint">No skills added yet. The eligibility checker needs them.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>CV</h3>
    <?php if ($student['cv_path']): ?>
        <p>
            <a class="btn btn-small btn-outline" target="_blank"
               href="<?php echo BASE_URL . '/' . e($student['cv_path']); ?>">Open my CV</a>
        </p>
        <p class="form-hint">Stored file: <?php echo e(basename($student['cv_path'])); ?></p>
    <?php else: ?>
        <p class="form-hint">No CV uploaded yet.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
