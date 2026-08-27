<?php
/**
 * Reusable search / filter form.
 * Expects $searchAction, $searchValues, $categories and $locations.
 */
?>
<div class="card">
    <form method="get" action="<?php echo $searchAction; ?>" class="search-form">
        <div class="form-group">
            <label for="keyword">Title contains</label>
            <input type="text" id="keyword" name="keyword" value="<?php echo e($searchValues['keyword']); ?>"
                   placeholder="e.g. developer">
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo e($category); ?>" <?php echo $searchValues['category'] === $category ? 'selected' : ''; ?>>
                        <?php echo e($category); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <select id="location" name="location">
                <option value="">All locations</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo e($location); ?>" <?php echo $searchValues['location'] === $location ? 'selected' : ''; ?>>
                        <?php echo e($location); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group search-actions">
            <button type="submit" class="btn">Search</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/student/internships.php">Reset</a>
        </div>
    </form>
</div>
