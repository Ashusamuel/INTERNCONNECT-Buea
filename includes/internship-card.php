<?php
/**
 * Reusable internship card.
 * Expects $internship (a row from the internships query with org_name).
 */
?>
<div class="card internship-card">
    <h3>
        <a href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $internship['internship_id']; ?>">
            <?php echo e($internship['title']); ?>
        </a>
    </h3>
    <p class="meta"><?php echo e($internship['org_name']); ?> &mdash; <?php echo e($internship['location']); ?></p>
    <p>
        <span class="badge"><?php echo e($internship['category']); ?></span>
        <?php if ($internship['duration']): ?>
            <span class="badge"><?php echo e($internship['duration']); ?></span>
        <?php endif; ?>
        <?php if ($internship['required_level']): ?>
            <span class="badge">Level <?php echo e($internship['required_level']); ?>+</span>
        <?php endif; ?>
    </p>
    <p><?php echo e(short_text($internship['description'])); ?></p>
    <p class="form-hint">
        <?php echo (int) $internship['positions']; ?> position(s) &middot;
        Deadline: <?php echo $internship['deadline'] ? e(date('d M Y', strtotime($internship['deadline']))) : 'open'; ?>
    </p>
    <a class="btn btn-small" href="<?php echo BASE_URL; ?>/student/internship-details.php?id=<?php echo (int) $internship['internship_id']; ?>">
        View details
    </a>
</div>
