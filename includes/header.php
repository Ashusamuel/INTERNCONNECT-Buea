<?php
/**
 * Opening HTML of every page.
 * Set $pageTitle before including this file.
 */
if (!isset($pageTitle)) {
    $pageTitle = SITE_NAME;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo e(SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="container">
    <?php show_flash(); ?>
