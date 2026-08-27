<?php
/**
 * Edit Internship Listing.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$internshipId = (int) get('id');
$internship   = get_internship($pdo, $internshipId);

if (!$internship || (int) $internship['org_id'] !== (int) $org['org_id']) {
    set_flash('error', 'Internship not found or access denied.');
    redirect('/organization/internships.php');
}

$errors = [];
$title         = post('title') ?: $internship['title'];
$category      = post('category') ?: $internship['category'];
$location      = post('location') ?: $internship['location'];
$duration      = post('duration') ?: $internship['duration'];
$requiredLevel = post('required_level') ?: $internship['required_level'];
$positions     = post('positions') ?: $internship['positions'];
$deadline      = post('deadline') ?: $internship['deadline'];
$description   = post('description') ?: $internship['description'];
$requirements  = post('requirements') ?: $internship['requirements'];
$allSkills     = all_skills($pdo);
$selectedSkills = internship_skill_ids($pdo, $internshipId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSkills = isset($_POST['skills']) ? array_map('intval', (array) $_POST['skills']) : [];
    $title         = post('title');
    $category      = post('category');
    $location      = post('location');
    $duration      = post('duration');
    $requiredLevel = post('required_level');
    $positions     = post('positions');
    $deadline      = post('deadline');
    $description   = post('description');
    $requirements  = post('requirements');

    if (empty($title)) {
        $errors[] = 'Title cannot be empty.';
    }
    if (empty($category)) {
        $errors[] = 'Category cannot be empty.';
    }

    if (!$errors) {
        $success = update_internship($pdo, $internshipId, $org['org_id'], [
            'title'          => $title,
            'category'       => $category,
            'location'       => $location,
            'duration'       => $duration,
            'required_level' => $requiredLevel,
            'positions'      => (int) $positions,
            'deadline'       => $deadline,
            'description'    => $description,
            'requirements'   => $requirements,
            'skills'         => $selectedSkills
        ]);

        if ($success) {
            set_flash('success', 'Internship listing updated successfully!');
            redirect('/organization/internships.php');
        } else {
            $errors[] = 'Failed to update internship listing.';
        }
    }
}

$pageTitle = 'Edit Internship - ' . $internship['title'];
include __DIR__ . '/../includes/header.php';
?>

<h1>Edit Internship Listing</h1>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width:750px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="title">Internship Title *</label>
            <input type="text" id="title" name="title" value="<?php echo e($title); ?>" required>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="category">Category / Field *</label>
                <input type="text" id="category" name="category" value="<?php echo e($category); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" value="<?php echo e($location); ?>" required>
            </div>
        </div>

        <div class="grid grid-3">
            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration" value="<?php echo e($duration); ?>">
            </div>
            <div class="form-group">
                <label for="required_level">Minimum Academic Level</label>
                <select id="required_level" name="required_level">
                    <option value="">Any Level</option>
                    <option value="100" <?php echo $requiredLevel === '100' ? 'selected' : ''; ?>>Level 100</option>
                    <option value="200" <?php echo $requiredLevel === '200' ? 'selected' : ''; ?>>Level 200</option>
                    <option value="300" <?php echo $requiredLevel === '300' ? 'selected' : ''; ?>>Level 300</option>
                    <option value="400" <?php echo $requiredLevel === '400' ? 'selected' : ''; ?>>Level 400</option>
                    <option value="500" <?php echo $requiredLevel === '500' ? 'selected' : ''; ?>>Level 500</option>
                    <option value="Masters" <?php echo $requiredLevel === 'Masters' ? 'selected' : ''; ?>>Masters</option>
                </select>
            </div>
            <div class="form-group">
                <label for="positions">Open Positions *</label>
                <input type="number" id="positions" name="positions" value="<?php echo e($positions); ?>" min="1" required>
            </div>
        </div>

        <div class="form-group">
            <label for="deadline">Application Deadline</label>
            <input type="date" id="deadline" name="deadline" value="<?php echo e($deadline); ?>">
        </div>

        <div class="form-group">
            <label for="description">Position Description *</label>
            <textarea id="description" name="description" rows="5" required><?php echo e($description); ?></textarea>
        </div>

        <div class="form-group">
            <label for="requirements">Candidate Requirements</label>
            <textarea id="requirements" name="requirements" rows="4"><?php echo e($requirements); ?></textarea>
        </div>

        <div class="form-group">
            <label>Required Skills</label>
            <p class="form-hint" style="margin-top:0; margin-bottom:8px;">Select the skills a candidate needs for this position. This helps us recommend your internship to matching students.</p>
            <div class="grid grid-3">
                <?php foreach ($allSkills as $skill): ?>
                    <label class="checkbox">
                        <input type="checkbox" name="skills[]" value="<?php echo (int) $skill['skill_id']; ?>"
                            <?php echo in_array((int) $skill['skill_id'], $selectedSkills, true) ? 'checked' : ''; ?>>
                        <?php echo e($skill['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group" style="margin-top:18px;">
            <button type="submit" class="btn btn-amber">Save Changes</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/organization/internships.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
