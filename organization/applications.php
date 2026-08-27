<?php
/**
 * View & Filter Applicants for Organization Internships.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('organization');

$org = get_organization($pdo, $_SESSION['user_id']);

if (!$org) {
    set_flash('error', 'Organization profile not found.');
    redirect('/index.php');
}

$filterInternshipId = get('internship_id');
$filterStatus       = get('status');

$postedInternships  = get_org_internships($pdo, $org['org_id']);
$applications       = get_org_applications($pdo, $org['org_id'], $filterInternshipId, $filterStatus);

$pageTitle = 'Applications Management';
include __DIR__ . '/../includes/header.php';
?>

<h1>Student Applications Received</h1>

<div class="card" style="margin-bottom:18px;">
    <form method="get" action="" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
        <div class="form-group" style="margin:0; flex:1; min-width:200px;">
            <label for="internship_id">Filter by Internship</label>
            <select id="internship_id" name="internship_id" onchange="this.form.submit()">
                <option value="">All Internships</option>
                <?php foreach ($postedInternships as $pi): ?>
                    <option value="<?php echo (int) $pi['internship_id']; ?>" <?php echo (string) $filterInternshipId === (string) $pi['internship_id'] ? 'selected' : ''; ?>>
                        <?php echo e($pi['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin:0; flex:1; min-width:180px;">
            <label for="status">Filter by Status</label>
            <select id="status" name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" <?php echo $filterStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="reviewed" <?php echo $filterStatus === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                <option value="accepted" <?php echo $filterStatus === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                <option value="rejected" <?php echo $filterStatus === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>

        <div style="margin:0;">
            <button type="submit" class="btn btn-amber">Filter</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/organization/applications.php">Reset</a>
        </div>
    </form>
</div>

<?php if (empty($applications)): ?>
    <div class="card" style="text-align:center; padding:36px;">
        <h3>No applications match your filter</h3>
        <p class="form-hint">Try clearing your filters or checking back later as students apply.</p>
    </div>
<?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Applicant Name</th>
                    <th>Internship Position</th>
                    <th>University & Level</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                    <?php
                    $statusClass = 'alert-info';
                    if ($app['status'] === 'accepted') {
                        $statusClass = 'alert-success';
                    } elseif ($app['status'] === 'rejected') {
                        $statusClass = 'alert-error';
                    } elseif ($app['status'] === 'reviewed') {
                        $statusClass = 'alert-warning';
                    }
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo e($app['full_name']); ?></strong><br>
                            <span class="form-hint"><?php echo e($app['email']); ?></span>
                        </td>
                        <td><?php echo e($app['title']); ?></td>
                        <td>
                            <?php echo e($app['university'] ?: 'Not set'); ?><br>
                            <span class="form-hint">Level: <?php echo e($app['level'] ?: 'N/A'); ?></span>
                        </td>
                        <td><?php echo e(date('d M Y, H:i', strtotime($app['applied_at']))); ?></td>
                        <td>
                            <span class="btn btn-small <?php echo $statusClass; ?>" style="cursor:default; text-transform:capitalize; padding:2px 10px;">
                                <?php echo e(ucfirst($app['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <a class="btn btn-small btn-amber" href="<?php echo BASE_URL; ?>/organization/application-details.php?id=<?php echo (int) $app['application_id']; ?>">
                                Review Application
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
