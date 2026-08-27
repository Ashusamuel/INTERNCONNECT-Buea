<?php
/**
 * Small helper functions used across the whole project.
 */

/** Escape text before printing it in HTML. */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Send the browser to another page and stop the script. */
function redirect($path)
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

/** Store a one-time message shown on the next page. */
function set_flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Print the stored message (if any) and remove it. */
function show_flash()
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    echo '<div class="alert alert-' . e($flash['type']) . '">' . e($flash['message']) . '</div>';
}

/** Trim a posted value and return an empty string when it is missing. */
function post($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

/** Trim a query-string value and return an empty string when it is missing. */
function get($key)
{
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : '';
}

/** Basic email check. */
function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/** Student profile row of the logged in student (or false). */
function get_student(PDO $pdo, $userId)
{
    $stmt = $pdo->prepare(
        'SELECT s.*, u.email FROM students s
         JOIN users u ON u.user_id = s.user_id
         WHERE s.user_id = ?'
    );
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/** All skills, ordered by name. */
function all_skills(PDO $pdo)
{
    return $pdo->query('SELECT skill_id, name FROM skills ORDER BY name')->fetchAll();
}

/** Skill ids chosen by one student. */
function student_skill_ids(PDO $pdo, $studentId)
{
    $stmt = $pdo->prepare('SELECT skill_id FROM student_skills WHERE student_id = ?');
    $stmt->execute([$studentId]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

/** Skill names chosen by one student. */
function student_skill_names(PDO $pdo, $studentId)
{
    $stmt = $pdo->prepare(
        'SELECT sk.name FROM student_skills ss
         JOIN skills sk ON sk.skill_id = ss.skill_id
         WHERE ss.student_id = ? ORDER BY sk.name'
    );
    $stmt->execute([$studentId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/** Replace the skills of a student with the given list of skill ids. */
function save_student_skills(PDO $pdo, $studentId, array $skillIds)
{
    $delete = $pdo->prepare('DELETE FROM student_skills WHERE student_id = ?');
    $delete->execute([$studentId]);

    if (!$skillIds) {
        return;
    }
    $insert = $pdo->prepare('INSERT INTO student_skills (student_id, skill_id) VALUES (?, ?)');
    foreach ($skillIds as $skillId) {
        $insert->execute([$studentId, (int) $skillId]);
    }
}

/** Skill ids chosen for an internship. */
function internship_skill_ids(PDO $pdo, $internshipId)
{
    $stmt = $pdo->prepare('SELECT skill_id FROM internship_skills WHERE internship_id = ?');
    $stmt->execute([$internshipId]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

/** Replace the skills of an internship with the given list of skill ids. */
function save_internship_skills(PDO $pdo, $internshipId, array $skillIds)
{
    $delete = $pdo->prepare('DELETE FROM internship_skills WHERE internship_id = ?');
    $delete->execute([$internshipId]);

    if (!$skillIds) {
        return;
    }
    $insert = $pdo->prepare('INSERT INTO internship_skills (internship_id, skill_id) VALUES (?, ?)');
    foreach ($skillIds as $skillId) {
        $insert->execute([$internshipId, (int) $skillId]);
    }
}

/**
 * How complete a student profile is, in percent.
 * Used by the dashboard to encourage students to finish their profile.
 */
function profile_completion(array $student, array $skillNames)
{
    $fields = [
        $student['full_name'],
        $student['phone'],
        $student['university'],
        $student['programme'],
        $student['level'],
        $student['location'],
        $student['bio'],
        $student['cv_path'],
        $skillNames ? 'yes' : '',
    ];
    $filled = 0;
    foreach ($fields as $value) {
        if ($value !== null && trim((string) $value) !== '') {
            $filled++;
        }
    }
    return (int) round($filled / count($fields) * 100);
}

/**
 * Validate and store an uploaded CV.
 * Returns the new relative path, or false when nothing was uploaded.
 * Validation problems are added to $errors.
 */
function handle_cv_upload(array $file, $studentId, array &$errors)
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'The CV could not be uploaded (error code ' . (int) $file['error'] . ').';
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        $errors[] = 'The CV must be smaller than 2 MB.';
        return false;
    }

    $allowed   = ['pdf', 'doc', 'docx'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed, true)) {
        $errors[] = 'The CV must be a PDF, DOC or DOCX file.';
        return false;
    }

    $folder = dirname(__DIR__) . '/uploads/cv/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $newName = 'cv_' . (int) $studentId . '_' . time() . '.' . $extension;
    if (!move_uploaded_file($file['tmp_name'], $folder . $newName)) {
        $errors[] = 'The CV could not be saved on the server.';
        return false;
    }

    // Only the path is stored in MySQL, never the file itself.
    return 'uploads/cv/' . $newName;
}

/** Shorten a text for the internship cards. */
function short_text($text, $limit = 140)
{
    $text = trim((string) $text);
    if (strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(substr($text, 0, $limit)) . '...';
}

/** Distinct categories used by active internships. */
function internship_categories(PDO $pdo)
{
    return $pdo->query(
        'SELECT DISTINCT category FROM internships WHERE is_active = 1 ORDER BY category'
    )->fetchAll(PDO::FETCH_COLUMN);
}

/** Distinct locations used by active internships. */
function internship_locations(PDO $pdo)
{
    return $pdo->query(
        'SELECT DISTINCT location FROM internships WHERE is_active = 1 ORDER BY location'
    )->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Search active internships by title keyword, category and location.
 * Empty filters are ignored. Everything is passed as a bound parameter.
 */
function search_internships(PDO $pdo, $keyword, $category, $location)
{
    $sql = 'SELECT i.*, o.org_name
            FROM internships i
            JOIN organizations o ON o.org_id = i.org_id
            WHERE i.is_active = 1';
    $params = [];

    if ($keyword !== '') {
        $sql .= ' AND i.title LIKE ?';
        $params[] = '%' . $keyword . '%';
    }
    if ($category !== '') {
        $sql .= ' AND i.category = ?';
        $params[] = $category;
    }
    if ($location !== '') {
        $sql .= ' AND i.location = ?';
        $params[] = $location;
    }

    $sql .= ' ORDER BY i.created_at DESC, i.internship_id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get recommended internships for a student based on matching skills.
 * Internships with 0 matching skills are not returned.
 * Ordered by number of matches DESC, then created_at DESC.
 */
function get_recommended_internships(PDO $pdo, $studentId)
{
    $sql = 'SELECT i.*, o.org_name, COUNT(isk.skill_id) as match_count
            FROM internships i
            JOIN organizations o ON o.org_id = i.org_id
            JOIN internship_skills isk ON isk.internship_id = i.internship_id
            JOIN student_skills ssk ON ssk.skill_id = isk.skill_id AND ssk.student_id = ?
            WHERE i.is_active = 1
            GROUP BY i.internship_id
            HAVING match_count > 0
            ORDER BY match_count DESC, i.created_at DESC';
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([(int) $studentId]);
    return $stmt->fetchAll();
}

/** One internship with its organization, or false. */
function get_internship(PDO $pdo, $internshipId)
{
    $stmt = $pdo->prepare(
        'SELECT i.*, o.org_name, o.sector, o.location AS org_location, o.website, o.description AS org_description
         FROM internships i
         JOIN organizations o ON o.org_id = i.org_id
         WHERE i.internship_id = ?'
    );
    $stmt->execute([(int) $internshipId]);
    return $stmt->fetch();
}

/** True when the deadline has passed. */
function is_expired($deadline)
{
    return $deadline !== null && $deadline < date('Y-m-d');
}

/** Count how many rows a simple table holds (used by the dashboards). */
function count_rows(PDO $pdo, $table)
{
    $allowed = ['users', 'students', 'organizations', 'internships', 'applications'];
    if (!in_array($table, $allowed, true)) {
        return 0;
    }
    return (int) $pdo->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();
}

// ============================================================
// Stage 5: Eligibility Checker
// ============================================================

/** Compare two academic levels. Returns true if $studentLevel >= $requiredLevel. */
function compare_academic_levels($studentLevel, $requiredLevel)
{
    $order = ['100' => 1, '200' => 2, '300' => 3, '400' => 4, '500' => 5, 'Masters' => 6];
    $studentRank  = isset($order[$studentLevel]) ? $order[$studentLevel] : 0;
    $requiredRank = isset($order[$requiredLevel]) ? $order[$requiredLevel] : 0;
    return $studentRank >= $requiredRank;
}

/** Check student eligibility for a specific internship. */
function check_student_eligibility(PDO $pdo, $studentId, $internshipId)
{
    $reasons = [];
    $eligible = true;

    $stmt = $pdo->prepare('SELECT * FROM students WHERE student_id = ?');
    $stmt->execute([(int) $studentId]);
    $student = $stmt->fetch();

    $internship = get_internship($pdo, $internshipId);

    if (!$student || !$internship) {
        return ['eligible' => false, 'reasons' => ['Invalid student or internship.'], 'applied' => false, 'saved' => false];
    }

    $applied = has_student_applied($pdo, $studentId, $internshipId);
    $saved   = is_internship_saved($pdo, $studentId, $internshipId);
    $expired = is_expired($internship['deadline']);

    if ($applied) {
        $reasons[] = 'You have already applied for this internship.';
        $eligible  = false;
    }

    if ($expired) {
        $reasons[] = 'The application deadline for this internship has passed.';
        $eligible  = false;
    }

    $skillNames = student_skill_names($pdo, $studentId);
    $completion = profile_completion($student, $skillNames);
    $completion_ok = ($completion >= 50);
    if (!$completion_ok) {
        $reasons[] = 'Your profile is only ' . $completion . '% complete. Minimum required is 50%.';
        $eligible  = false;
    }

    $cv_ok = !empty($student['cv_path']);
    if (!$cv_ok) {
        $reasons[] = 'You have not uploaded a CV yet.';
        $eligible  = false;
    }

    $level_ok = true;
    if (!empty($internship['required_level'])) {
        if (empty($student['level']) || !compare_academic_levels($student['level'], $internship['required_level'])) {
            $level_ok  = false;
            $eligible  = false;
            $reasons[] = 'Academic level requirement not met (Required: Level ' . e($internship['required_level']) . ', Your Level: ' . e($student['level'] ?: 'Not set') . ').';
        }
    }

    return [
        'eligible'      => $eligible,
        'reasons'       => $reasons,
        'applied'       => $applied,
        'saved'         => $saved,
        'expired'       => $expired,
        'completion_ok' => $completion_ok,
        'cv_ok'         => $cv_ok,
        'level_ok'      => $level_ok,
        'completion'    => $completion
    ];
}

// ============================================================
// Stage 6: Applications System
// ============================================================

/** Check if student has applied for an internship. */
function has_student_applied(PDO $pdo, $studentId, $internshipId)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE student_id = ? AND internship_id = ?');
    $stmt->execute([(int) $studentId, (int) $internshipId]);
    return (int) $stmt->fetchColumn() > 0;
}

/** Process an application submission. */
function apply_for_internship(PDO $pdo, $studentId, $internshipId, $coverLetter)
{
    if (has_student_applied($pdo, $studentId, $internshipId)) {
        return false;
    }
    $stmt = $pdo->prepare(
        'INSERT INTO applications (student_id, internship_id, cover_letter, status) VALUES (?, ?, ?, "pending")'
    );
    return $stmt->execute([(int) $studentId, (int) $internshipId, $coverLetter]);
}

/** Get all applications submitted by a student. */
function get_student_applications(PDO $pdo, $studentId)
{
    $stmt = $pdo->prepare(
        'SELECT a.*, i.title, i.category, i.location, o.org_name
         FROM applications a
         JOIN internships i ON i.internship_id = a.internship_id
         JOIN organizations o ON o.org_id = i.org_id
         WHERE a.student_id = ?
         ORDER BY a.applied_at DESC'
    );
    $stmt->execute([(int) $studentId]);
    return $stmt->fetchAll();
}

/** Get single application by ID. */
function get_application_by_id(PDO $pdo, $applicationId)
{
    $stmt = $pdo->prepare(
        'SELECT a.*, i.title, i.category, i.location, i.org_id, o.org_name,
                s.full_name, s.university, s.programme, s.level, s.cv_path, s.phone, u.email
         FROM applications a
         JOIN internships i ON i.internship_id = a.internship_id
         JOIN organizations o ON o.org_id = i.org_id
         JOIN students s ON s.student_id = a.student_id
         JOIN users u ON u.user_id = s.user_id
         WHERE a.application_id = ?'
    );
    $stmt->execute([(int) $applicationId]);
    return $stmt->fetch();
}

// ============================================================
// Stage 7: Saved Internships System
// ============================================================

/** Check if an internship is bookmarked. */
function is_internship_saved(PDO $pdo, $studentId, $internshipId)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM saved_internships WHERE student_id = ? AND internship_id = ?');
    $stmt->execute([(int) $studentId, (int) $internshipId]);
    return (int) $stmt->fetchColumn() > 0;
}

/** Toggle save/unsave bookmark. */
function toggle_save_internship(PDO $pdo, $studentId, $internshipId)
{
    if (is_internship_saved($pdo, $studentId, $internshipId)) {
        $stmt = $pdo->prepare('DELETE FROM saved_internships WHERE student_id = ? AND internship_id = ?');
        $stmt->execute([(int) $studentId, (int) $internshipId]);
        return false; // Now unsaved
    } else {
        $stmt = $pdo->prepare('INSERT INTO saved_internships (student_id, internship_id) VALUES (?, ?)');
        $stmt->execute([(int) $studentId, (int) $internshipId]);
        return true; // Now saved
    }
}

/** Get list of saved internships for student. */
function get_saved_internships(PDO $pdo, $studentId)
{
    $stmt = $pdo->prepare(
        'SELECT i.*, o.org_name, si.saved_at
         FROM saved_internships si
         JOIN internships i ON i.internship_id = si.internship_id
         JOIN organizations o ON o.org_id = i.org_id
         WHERE si.student_id = ? AND i.is_active = 1
         ORDER BY si.saved_at DESC'
    );
    $stmt->execute([(int) $studentId]);
    return $stmt->fetchAll();
}

// ============================================================
// Stage 8: Organization Auth & Profile
// ============================================================

/** Get organization profile by user ID. */
function get_organization(PDO $pdo, $userId)
{
    $stmt = $pdo->prepare(
        'SELECT o.*, u.email FROM organizations o
         JOIN users u ON u.user_id = o.user_id
         WHERE o.user_id = ?'
    );
    $stmt->execute([(int) $userId]);
    return $stmt->fetch();
}

/** Get organization profile by org ID. */
function get_organization_by_id(PDO $pdo, $orgId)
{
    $stmt = $pdo->prepare(
        'SELECT o.*, u.email FROM organizations o
         JOIN users u ON u.user_id = o.user_id
         WHERE o.org_id = ?'
    );
    $stmt->execute([(int) $orgId]);
    return $stmt->fetch();
}

/** Update organization profile details. */
function update_organization_profile(PDO $pdo, $orgId, array $data)
{
    $stmt = $pdo->prepare(
        'UPDATE organizations
         SET org_name = ?, sector = ?, location = ?, phone = ?, website = ?, description = ?
         WHERE org_id = ?'
    );
    return $stmt->execute([
        $data['org_name'],
        $data['sector'],
        $data['location'],
        $data['phone'],
        $data['website'],
        $data['description'],
        (int) $orgId
    ]);
}

// ============================================================
// Stage 9: Post & Manage Internships (Organization)
// ============================================================

/** Get all internships posted by an organization. */
function get_org_internships(PDO $pdo, $orgId)
{
    $stmt = $pdo->prepare(
        'SELECT i.*,
                (SELECT COUNT(*) FROM applications a WHERE a.internship_id = i.internship_id) AS applicant_count
         FROM internships i
         WHERE i.org_id = ?
         ORDER BY i.created_at DESC'
    );
    $stmt->execute([(int) $orgId]);
    return $stmt->fetchAll();
}

/** Post a new internship position. */
function create_internship(PDO $pdo, $orgId, array $data)
{
    $stmt = $pdo->prepare(
        'INSERT INTO internships (org_id, title, description, requirements, category, location, duration, required_level, positions, deadline, is_active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)'
    );
    $success = $stmt->execute([
        (int) $orgId,
        $data['title'],
        $data['description'],
        $data['requirements'],
        $data['category'],
        $data['location'],
        $data['duration'],
        $data['required_level'] ?: null,
        (int) $data['positions'],
        $data['deadline'] ?: null
    ]);
    
    if ($success && isset($data['skills'])) {
        $internshipId = (int) $pdo->lastInsertId();
        save_internship_skills($pdo, $internshipId, $data['skills']);
    }
    
    return $success;
}

/** Update an existing internship post. */
function update_internship(PDO $pdo, $internshipId, $orgId, array $data)
{
    $stmt = $pdo->prepare(
        'UPDATE internships
         SET title = ?, description = ?, requirements = ?, category = ?, location = ?, duration = ?, required_level = ?, positions = ?, deadline = ?
         WHERE internship_id = ? AND org_id = ?'
    );
    $success = $stmt->execute([
        $data['title'],
        $data['description'],
        $data['requirements'],
        $data['category'],
        $data['location'],
        $data['duration'],
        $data['required_level'] ?: null,
        (int) $data['positions'],
        $data['deadline'] ?: null,
        (int) $internshipId,
        (int) $orgId
    ]);
    
    if ($success && isset($data['skills'])) {
        save_internship_skills($pdo, $internshipId, $data['skills']);
    }
    
    return $success;
}

/** Toggle active status (1 or 0) of an internship. */
function toggle_internship_status(PDO $pdo, $internshipId, $orgId)
{
    $stmt = $pdo->prepare('UPDATE internships SET is_active = 1 - is_active WHERE internship_id = ? AND org_id = ?');
    return $stmt->execute([(int) $internshipId, (int) $orgId]);
}

// ============================================================
// Stage 10: Application Management (Organization)
// ============================================================

/** Get applications received for an organization's internships. */
function get_org_applications(PDO $pdo, $orgId, $internshipId = null, $status = null)
{
    $sql = 'SELECT a.*, i.title, s.full_name, s.university, s.programme, s.level, s.cv_path, u.email
            FROM applications a
            JOIN internships i ON i.internship_id = a.internship_id
            JOIN students s ON s.student_id = a.student_id
            JOIN users u ON u.user_id = s.user_id
            WHERE i.org_id = ?';
    $params = [(int) $orgId];

    if (!empty($internshipId)) {
        $sql .= ' AND i.internship_id = ?';
        $params[] = (int) $internshipId;
    }
    if (!empty($status)) {
        $sql .= ' AND a.status = ?';
        $params[] = $status;
    }

    $sql .= ' ORDER BY a.applied_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Update the status of an application. */
function update_application_status(PDO $pdo, $applicationId, $orgId, $status)
{
    $allowed = ['pending', 'reviewed', 'accepted', 'rejected'];
    if (!in_array($status, $allowed, true)) {
        return false;
    }

    $stmt = $pdo->prepare(
        'UPDATE applications a
         JOIN internships i ON i.internship_id = a.internship_id
         SET a.status = ?
         WHERE a.application_id = ? AND i.org_id = ?'
    );
    return $stmt->execute([$status, (int) $applicationId, (int) $orgId]);
}

// ============================================================
// Stage 11: Admin System
// ============================================================

/** Statistics overview for admin dashboard. */
function get_admin_stats(PDO $pdo)
{
    return [
        'total_users'         => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'total_students'      => (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn(),
        'total_organizations' => (int) $pdo->query('SELECT COUNT(*) FROM organizations')->fetchColumn(),
        'unverified_orgs'     => (int) $pdo->query('SELECT COUNT(*) FROM organizations WHERE is_verified = 0')->fetchColumn(),
        'total_internships'   => (int) $pdo->query('SELECT COUNT(*) FROM internships')->fetchColumn(),
        'total_applications'  => (int) $pdo->query('SELECT COUNT(*) FROM applications')->fetchColumn(),
    ];
}

/** Get list of all organizations for admin view. */
function get_all_organizations(PDO $pdo)
{
    return $pdo->query(
        'SELECT o.*, u.email, u.is_active,
                (SELECT COUNT(*) FROM internships i WHERE i.org_id = o.org_id) AS internship_count
         FROM organizations o
         JOIN users u ON u.user_id = o.user_id
         ORDER BY o.created_at DESC'
    )->fetchAll();
}

/** Toggle organization verification status. */
function toggle_organization_verification(PDO $pdo, $orgId)
{
    $stmt = $pdo->prepare('UPDATE organizations SET is_verified = 1 - is_verified WHERE org_id = ?');
    return $stmt->execute([(int) $orgId]);
}

/** Get list of all platform users. */
function get_all_users(PDO $pdo)
{
    return $pdo->query(
        'SELECT u.*,
                COALESCE(s.full_name, o.org_name, "Administrator") AS display_name
         FROM users u
         LEFT JOIN students s ON s.user_id = u.user_id
         LEFT JOIN organizations o ON o.user_id = u.user_id
         ORDER BY u.created_at DESC'
    )->fetchAll();
}

/** Toggle active status (is_active) of a user account. */
function toggle_user_active(PDO $pdo, $userId)
{
    $stmt = $pdo->prepare('UPDATE users SET is_active = 1 - is_active WHERE user_id = ?');
    return $stmt->execute([(int) $userId]);
}

/** Get list of all internships for admin view. */
function get_all_internships_admin(PDO $pdo)
{
    return $pdo->query(
        'SELECT i.*, o.org_name,
                (SELECT COUNT(*) FROM applications a WHERE a.internship_id = i.internship_id) AS applicant_count
         FROM internships i
         JOIN organizations o ON o.org_id = i.org_id
         ORDER BY i.created_at DESC'
    )->fetchAll();
}

