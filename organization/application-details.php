<?php
/**
 * Review Application Details & Update Application Status.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$applicationId = (int) get('id');
$application   = get_application_by_id($pdo, $applicationId);

if (!$application || (int) $application['org_id'] !== (int) $org['org_id']) {
    set_flash('error', 'Application not found or access denied.');
    redirect('/organization/applications.php');
}

// Handle status change submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'update_status') {
    $newStatus = post('status');
    $updated   = update_application_status($pdo, $applicationId, $org['org_id'], $newStatus);

    if ($updated) {
        set_flash('success', 'Application status updated to ' . ucfirst($newStatus) . '.');
        redirect('/organization/application-details.php?id=' . $applicationId);
    } else {
        set_flash('error', 'Failed to update application status.');
    }
}

$studentSkills = student_skill_names($pdo, $application['student_id']);

$pageTitle = 'Review Application - ' . $application['full_name'];
include __DIR__ . '/../includes/header.php';
?>

<p><a href="<?php echo BASE_URL; ?>/organization/applications.php">&larr; Back to applications list</a></p>

<div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
    <div>
        <h1>Application: <?php echo e($application['full_name']); ?></h1>
        <p class="meta">Position: <strong><?php echo e($application['title']); ?></strong> | Submitted on <?php echo e(date('d M Y, H:i', strtotime($application['applied_at']))); ?></p>
    </div>
    <div>
        <?php
        $statusClass = 'alert-info';
        if ($application['status'] === 'accepted') {
            $statusClass = 'alert-success';
        } elseif ($application['status'] === 'rejected') {
            $statusClass = 'alert-error';
        } elseif ($application['status'] === 'reviewed') {
            $statusClass = 'alert-warning';
        }
        ?>
        <span class="btn <?php echo $statusClass; ?>" style="cursor:default; text-transform:capitalize;">
            Status: <?php echo e(ucfirst($application['status'])); ?>
        </span>
    </div>
</div>

<div class="grid grid-2">
    <div>
        <div class="card">
            <h3>Cover Letter / Interest Statement</h3>
            <p><?php echo nl2br(e($application['cover_letter'] ?: 'No cover letter provided.')); ?></p>
        </div>

        <div class="card">
            <h3>Update Application Decision</h3>
            <form method="post" action="">
                <input type="hidden" name="action" value="update_status">
                <div class="form-group">
                    <label for="status">Change Status</label>
                    <select id="status" name="status">
                        <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                        <option value="reviewed" <?php echo $application['status'] === 'reviewed' ? 'selected' : ''; ?>>Mark as Reviewed</option>
                        <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>Accept Candidate</option>
                        <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Reject Candidate</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top:12px;">
                    <button type="submit" class="btn btn-amber">Save New Status</button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Applicant Background</h3>
            <table class="table">
                <tr><th>Full Name</th><td><?php echo e($application['full_name']); ?></td></tr>
                <tr><th>Email</th><td><a href="mailto:<?php echo e($application['email']); ?>"><?php echo e($application['email']); ?></a></td></tr>
                <tr><th>Phone</th><td><?php echo e($application['phone'] ?: 'Not set'); ?></td></tr>
                <tr><th>University</th><td><?php echo e($application['university'] ?: 'Not set'); ?></td></tr>
                <tr><th>Programme</th><td><?php echo e($application['programme'] ?: 'Not set'); ?></td></tr>
                <tr><th>Level</th><td>Level <?php echo e($application['level'] ?: 'N/A'); ?></td></tr>
                <tr>
                    <th>CV Document</th>
                    <td>
                        <?php if (!empty($application['cv_path'])): ?>
                            <a class="btn btn-small btn-amber" href="<?php echo BASE_URL . '/' . e($application['cv_path']); ?>" target="_blank" rel="noopener">
                                Download / View CV
                            </a>
                        <?php else: ?>
                            <span class="form-hint">No CV uploaded.</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h3>Applicant Skills</h3>
            <?php if (empty($studentSkills)): ?>
                <p class="form-hint">No skills listed on student profile.</p>
            <?php else: ?>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    <?php foreach ($studentSkills as $skill): ?>
                        <span class="btn btn-small btn-outline" style="cursor:default;"><?php echo e($skill); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
