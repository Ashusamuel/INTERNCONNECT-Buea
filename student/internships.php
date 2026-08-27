<?php
/**
 * Page 5 - Browse internships.
 * Shows every active internship together with the search form.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$categories = internship_categories($pdo);
$locations  = internship_locations($pdo);
$internships = search_internships($pdo, '', '', '');

$pageTitle = 'Browse internships';
include __DIR__ . '/../includes/header.php';
?>

<h1>Browse internships</h1>
<p class="form-hint"><?php echo count($internships); ?> internship(s) currently open in Buea.</p>

<?php
$searchAction = BASE_URL . '/student/search.php';
$searchValues = ['keyword' => '', 'category' => '', 'location' => ''];
include __DIR__ . '/../includes/search-form.php';
?>

<?php if ($internships): ?>
    <div class="grid grid-2">
        <?php foreach ($internships as $internship): ?>
            <?php include __DIR__ . '/../includes/internship-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">There are no active internships at the moment.</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
