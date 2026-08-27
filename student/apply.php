<?php
/**
 * Student Application Submission.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('student');

$studentId    = (int) $_SESSION['user_id'];
$student      = get_student($pdo, $studentId);
$internshipId = (int) get('id');
$internship   = get_internship($pdo, $internshipId);

if (!$internship || (int) $internship['is_active'] !== 1) {
    set_flash('error', 'That internship is not available.');
    redirect('/student/internships.php');
}

$eligibility = check_student_eligibility($pdo, $student['student_id'], $internshipId);

if (!$eligibility['eligible']) {
    set_flash('error', 'You cannot apply for this internship: ' . implode(' ', $eligibility['reasons']));
    redirect('/student/internship-details.php?id=' . $internshipId);
}

$errors = [];
$coverLetter = post('cover_letter');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($coverLetter)) {
        $errors[] = 'Please write a brief cover letter explaining why you are applying.';
    } elseif (strlen($coverLetter) < 20) {
        $errors[] = 'Your cover letter should be at least 20 characters long.';
    } else {
        $success = apply_for_internship($pdo, $student['student_id'], $internshipId, $coverLetter);
        if ($success) {
            set_flash('success', 'Your application for ' . $internship['title'] . ' has been submitted!');
            redirect('/student/applications.php');
        } else {
            $errors[] = 'An error occurred while submitting your application. Please try again.';
        }
    }
}

$pageTitle = 'Apply - ' . $internship['title'];
include __DIR__ . '/../includes/header.php';
?>

<p><a href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo $internshipId; ?>">&larr; Back to details</a></p>

<h1>Apply for <?php echo e($internship['title']); ?></h1>
<p class="meta"><?php echo e($internship['org_name']); ?> &mdash; <?php echo e($internship['location']); ?></p>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid grid-2">
    <div class="card">
        <h3>Application Form</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="cover_letter">Cover Letter / Statement of Interest</label>
                <textarea id="cover_letter" name="cover_letter" rows="8" placeholder="Introduce yourself, highlight relevant skills, and explain why you are interested in this position..."><?php echo e($coverLetter); ?></textarea>
                <span class="form-hint">Minimum 20 characters. Explain why your background matches this internship.</span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-amber">Submit Application</button>
                <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo $internshipId; ?>">Cancel</a>
            </div>
        </form>
    </div>

    <div>
        <div class="card">
            <h3>Your Profile Information</h3>
            <p class="form-hint">The organization will review this profile data along with your application:</p>
            <table class="table">
                <tr><th>Full Name</th><td><?php echo e($student['full_name']); ?></td></tr>
                <tr><th>University</th><td><?php echo e($student['university']); ?></td></tr>
                <tr><th>Programme</th><td><?php echo e($student['programme']); ?></td></tr>
                <tr><th>Level</th><td><?php echo e($student['level']); ?></td></tr>
                <tr><th>CV Document</th><td><?php echo $student['cv_path'] ? '<a href="' . BASE_URL . '/' . e($student['cv_path']) . '" target="_blank">View Uploaded CV</a>' : 'None'; ?></td></tr>
            </table>
        </div>

        <div class="card">
            <h3>Internship Summary</h3>
            <p><strong>Category:</strong> <?php echo e($internship['category']); ?></p>
            <p><strong>Duration:</strong> <?php echo e($internship['duration'] ?: 'N/A'); ?></p>
            <p><strong>Deadline:</strong> <?php echo $internship['deadline'] ? e(date('d M Y', strtotime($internship['deadline']))) : 'Open'; ?></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
