<?php
/**
 * Edit Organization Profile.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$errors      = [];
$orgName     = post('org_name') ?: $org['org_name'];
$sector      = post('sector') ?: $org['sector'];
$location    = post('location') ?: $org['location'];
$phone       = post('phone') ?: $org['phone'];
$website     = post('website') ?: $org['website'];
$description = post('description') ?: $org['description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orgName     = post('org_name');
    $sector      = post('sector');
    $location    = post('location');
    $phone       = post('phone');
    $website     = post('website');
    $description = post('description');

    if (empty($orgName)) {
        $errors[] = 'Organization name cannot be empty.';
    }

    if (!$errors) {
        $success = update_organization_profile($pdo, $org['org_id'], [
            'org_name'    => $orgName,
            'sector'      => $sector,
            'location'    => $location,
            'phone'       => $phone,
            'website'     => $website,
            'description' => $description
        ]);

        if ($success) {
            $_SESSION['name'] = $orgName; // update session display name
            set_flash('success', 'Organization profile updated successfully.');
            redirect('/organization/profile.php');
        } else {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
}

$pageTitle = 'Edit Profile - ' . $org['org_name'];
include __DIR__ . '/../includes/header.php';
?>

<h1>Edit Organization Profile</h1>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width:700px; margin:0 auto;">
    <form method="post" action="">
        <div class="form-group">
            <label for="org_name">Organization / Company Name *</label>
            <input type="text" id="org_name" name="org_name" value="<?php echo e($orgName); ?>" required>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="sector">Industry / Sector</label>
                <input type="text" id="sector" name="sector" value="<?php echo e($sector); ?>" placeholder="e.g. Technology, Finance, Health">
            </div>
            <div class="form-group">
                <label for="location">Office Location</label>
                <input type="text" id="location" name="location" value="<?php echo e($location); ?>" placeholder="e.g. Molyko, Buea">
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo e($phone); ?>" placeholder="e.g. 677100200">
            </div>
            <div class="form-group">
                <label for="website">Company Website</label>
                <input type="url" id="website" name="website" value="<?php echo e($website); ?>" placeholder="https://example.cm">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Company Overview / Description</label>
            <textarea id="description" name="description" rows="5"><?php echo e($description); ?></textarea>
        </div>

        <div class="form-group" style="margin-top:18px;">
            <button type="submit" class="btn btn-amber">Save Changes</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/organization/profile.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
