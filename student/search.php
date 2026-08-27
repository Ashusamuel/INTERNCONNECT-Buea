<?php
/**
 * Page 6 - Internship search results.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$keyword  = get('keyword');
$category = get('category');
$location = get('location');

$categories  = internship_categories($pdo);
$locations   = internship_locations($pdo);
$internships = search_internships($pdo, $keyword, $category, $location);

$pageTitle = 'Search results';
include __DIR__ . '/../includes/header.php';
?>

<h1>Search results</h1>

<?php
$searchAction = BASE_URL . '/student/search.php';
$searchValues = ['keyword' => $keyword, 'category' => $category, 'location' => $location];
include __DIR__ . '/../includes/search-form.php';
?>

<p class="form-hint">
    <?php echo count($internships); ?> result(s)
    <?php if ($keyword !== ''): ?> for &quot;<?php echo e($keyword); ?>&quot;<?php endif; ?>
    <?php if ($category !== ''): ?> in <?php echo e($category); ?><?php endif; ?>
    <?php if ($location !== ''): ?> at <?php echo e($location); ?><?php endif; ?>.
</p>

<?php if ($internships): ?>
    <div class="grid grid-2">
        <?php foreach ($internships as $internship): ?>
            <?php include __DIR__ . '/../includes/internship-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        No internship matches your search. Try a different keyword or remove a filter.
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
