<?php
/**
 * Post New Internship.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$errors = [];
$title         = post('title');
$category      = post('category');
$location      = post('location') ?: $org['location'];
$duration      = post('duration');
$requiredLevel = post('required_level');
$positions     = post('positions') ?: '1';
$deadline      = post('deadline');
$description   = post('description');
$requirements  = post('requirements');
$allSkills     = all_skills($pdo);
$selectedSkills = isset($_POST['skills']) ? array_map('intval', (array) $_POST['skills']) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($title)) {
        $errors[] = 'Internship title is required.';
    }
    if (empty($category)) {
        $errors[] = 'Category is required.';
    }
    if (empty($location)) {
        $errors[] = 'Location is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }

    if (!$errors) {
        $success = create_internship($pdo, $org['org_id'], [
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
            set_flash('success', 'Internship opportunity posted successfully!');
            redirect('/organization/internships.php');
        } else {
            $errors[] = 'Failed to create internship listing. Please check your data and try again.';
        }
    }
}

$pageTitle = 'Post New Internship';
include __DIR__ . '/../includes/header.php';
?>

<h1>Post a New Internship Position</h1>

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
            <input type="text" id="title" name="title" value="<?php echo e($title); ?>" placeholder="e.g. Web Development Intern" required>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="category">Category / Field *</label>
                <input type="text" id="category" name="category" value="<?php echo e($category); ?>" placeholder="e.g. Information Technology, Finance, Marketing" required>
            </div>
            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" value="<?php echo e($location); ?>" placeholder="e.g. Molyko, Buea" required>
            </div>
        </div>

        <div class="grid grid-3">
            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration" value="<?php echo e($duration); ?>" placeholder="e.g. 3 months">
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
            <textarea id="description" name="description" rows="5" placeholder="Describe the responsibilities, project scope, and learning opportunities..." required><?php echo e($description); ?></textarea>
        </div>

        <div class="form-group">
            <label for="requirements">Candidate Requirements</label>
            <textarea id="requirements" name="requirements" rows="4" placeholder="List required skills, qualifications, or prerequisites..."><?php echo e($requirements); ?></textarea>
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
            <button type="submit" class="btn btn-amber">Publish Internship Listing</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/organization/internships.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
