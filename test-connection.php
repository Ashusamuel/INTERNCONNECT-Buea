<?php
/**
 * Temporary Stage 1 test page.
 * Open http://localhost/internconnect/test-connection.php
 * Delete this file once the project is finished.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Database test';
include __DIR__ . '/includes/header.php';

$tables = ['users', 'students', 'organizations'];
?>

<h1>Stage 1 - database test</h1>

<div class="alert alert-success">
    PHP connected to MySQL database <strong><?php echo e(DB_NAME); ?></strong>
    on <?php echo e(DB_HOST); ?> (server version
    <?php echo e($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)); ?>).
</div>

<div class="card">
    <h3>Tables</h3>
    <table class="table">
        <tr><th>Table</th><th>Exists</th><th>Rows</th></tr>
        <?php foreach ($tables as $table): ?>
            <?php
            $exists = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table))->fetch() !== false;
            $rows   = $exists ? count_rows($pdo, $table) : 0;
            ?>
            <tr>
                <td><?php echo e($table); ?></td>
                <td>
                    <span class="badge <?php echo $exists ? 'badge-success' : 'badge-error'; ?>">
                        <?php echo $exists ? 'yes' : 'missing'; ?>
                    </span>
                </td>
                <td><?php echo (int) $rows; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
