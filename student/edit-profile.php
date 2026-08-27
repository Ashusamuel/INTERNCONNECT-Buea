<?php
/**
 * Page 14 - Edit student profile (details, skills and CV upload).
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('student');

$student  = get_student($pdo, $_SESSION['user_id']);
$skills   = all_skills($pdo);
$selected = student_skill_ids($pdo, $student['student_id']);
$levels   = ['100', '200', '300', '400', '500', 'Masters'];
$errors   = [];

// Values shown in the form (posted values win, otherwise the saved ones).
$values = [
    'full_name'  => $student['full_name'],
    'phone'      => (string) $student['phone'],
    'university' => (string) $student['university'],
    'programme'  => (string) $student['programme'],
    'level'      => (string) $student['level'],
    'location'   => (string) $student['location'],
    'bio'        => (string) $student['bio'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $ignored) {
        $values[$key] = post($key);
    }
    $selected = isset($_POST['skills']) ? array_map('intval', (array) $_POST['skills']) : [];

    // ---- validation -------------------------------------------------
    if (strlen($values['full_name']) < 3) {
        $errors[] = 'Full name must be at least 3 characters.';
    }
    if ($values['phone'] !== '' && !preg_match('/^[0-9 +\-]{6,20}$/', $values['phone'])) {
        $errors[] = 'Phone number may only contain digits, spaces, + and -.';
    }
    if ($values['level'] !== '' && !in_array($values['level'], $levels, true)) {
        $errors[] = 'Please choose a valid level.';
    }
    if (strlen($values['bio']) > 1000) {
        $errors[] = 'The description must be shorter than 1000 characters.';
    }

    // Only keep skill ids that really exist.
    $validIds = array_map(function ($row) { return (int) $row['skill_id']; }, $skills);
    $selected = array_values(array_intersect($selected, $validIds));

    // ---- CV upload ----------------------------------------------------
    $cvPath = $student['cv_path'];
    if (isset($_FILES['cv'])) {
        $uploaded = handle_cv_upload($_FILES['cv'], $student['student_id'], $errors);
        if ($uploaded !== false) {
            // Remove the previous file so the uploads folder stays clean.
            if ($student['cv_path']) {
                $old = dirname(__DIR__) . '/' . $student['cv_path'];
                if (is_file($old)) {
                    unlink($old);
                }
            }
            $cvPath = $uploaded;
        }
    }

    // ---- save ----------------------------------------------------------
    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $sql = 'UPDATE students
                    SET full_name = ?, phone = ?, university = ?, programme = ?,
                        level = ?, location = ?, bio = ?, cv_path = ?
                    WHERE student_id = ?';
            $pdo->prepare($sql)->execute([
                $values['full_name'],
                $values['phone'] !== '' ? $values['phone'] : null,
                $values['university'] !== '' ? $values['university'] : null,
                $values['programme'] !== '' ? $values['programme'] : null,
                $values['level'] !== '' ? $values['level'] : null,
                $values['location'] !== '' ? $values['location'] : null,
                $values['bio'] !== '' ? $values['bio'] : null,
                $cvPath,
                $student['student_id'],
            ]);

            save_student_skills($pdo, $student['student_id'], $selected);

            $pdo->commit();

            // The navbar greeting uses the session copy of the name.
            $_SESSION['name'] = $values['full_name'];

            set_flash('success', 'Your profile has been updated.');
            redirect('/student/profile.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'The profile could not be saved. Please try again.';
        }
    }
}

$pageTitle = 'Edit profile';
include __DIR__ . '/../includes/header.php';
?>

<h1>Edit my profile</h1>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <div><?php echo e($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="max-width:720px; margin:0 auto;">
<form method="post" action="" enctype="multipart/form-data">
    <div class="card">
        <h3>Personal details</h3>

        <div class="form-group">
            <label for="full_name">Full name *</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo e($values['full_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone number</label>
            <input type="text" id="phone" name="phone" value="<?php echo e($values['phone']); ?>">
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?php echo e($values['location']); ?>"
                   placeholder="e.g. Molyko, Buea">
        </div>
    </div>

    <div class="card">
        <h3>Studies</h3>

        <div class="form-group">
            <label for="university">University / school</label>
            <input type="text" id="university" name="university" value="<?php echo e($values['university']); ?>"
                   placeholder="e.g. University of Buea">
        </div>

        <div class="form-group">
            <label for="programme">Programme</label>
            <input type="text" id="programme" name="programme" value="<?php echo e($values['programme']); ?>"
                   placeholder="e.g. Software Engineering">
        </div>

        <div class="form-group">
            <label for="level">Level</label>
            <select id="level" name="level">
                <option value="">-- choose --</option>
                <?php foreach ($levels as $level): ?>
                    <option value="<?php echo e($level); ?>" <?php echo $values['level'] === $level ? 'selected' : ''; ?>>
                        <?php echo e($level); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="bio">About me</label>
            <textarea id="bio" name="bio" placeholder="A few lines about your studies and interests."><?php echo e($values['bio']); ?></textarea>
        </div>
    </div>

    <div class="card">
        <h3>Skills</h3>
        <p class="form-hint">Tick every skill you have. They are compared with the internship requirements.</p>
        <div class="grid grid-3">
            <?php foreach ($skills as $skill): ?>
                <label class="checkbox">
                    <input type="checkbox" name="skills[]" value="<?php echo (int) $skill['skill_id']; ?>"
                        <?php echo in_array((int) $skill['skill_id'], $selected, true) ? 'checked' : ''; ?>>
                    <?php echo e($skill['name']); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h3>CV</h3>
        <?php if ($student['cv_path']): ?>
            <p class="form-hint">
                Current CV: <a target="_blank" href="<?php echo BASE_URL . '/' . e($student['cv_path']); ?>">
                    <?php echo e(basename($student['cv_path'])); ?></a>
                (uploading a new file replaces it)
            </p>
        <?php endif; ?>
        <div class="form-group">
            <label for="cv">Upload CV</label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
            <p class="form-hint">PDF, DOC or DOCX, maximum 2 MB.</p>
        </div>
    </div>

    <p>
        <button type="submit" class="btn">Save profile</button>
        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/student/profile.php">Cancel</a>
    </p>
</form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
