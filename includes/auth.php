<?php
/**
 * Session handling and role based access control.
 * Include this file (after config/database.php) on every page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

/** True when somebody is logged in. */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/** Role of the current user: student, organization, admin or empty. */
function current_role()
{
    return isset($_SESSION['role']) ? $_SESSION['role'] : '';
}

/** Display name of the current user. */
function current_name()
{
    return isset($_SESSION['name']) ? $_SESSION['name'] : '';
}

/** Save the logged in user in the session. */
function login_user($userId, $role, $name)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $userId;
    $_SESSION['role']    = $role;
    $_SESSION['name']    = $name;
}

/** Clear the session completely. */
function logout_user()
{
    $_SESSION = [];
    session_destroy();
}

/** Stop the page unless the visitor is logged in. */
function require_login()
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect('/login.php');
    }
}

/** Stop the page unless the visitor has the given role. */
function require_role($role)
{
    require_login();
    if (current_role() !== $role) {
        set_flash('error', 'You are not allowed to open that page.');
        redirect('/index.php');
    }
}
