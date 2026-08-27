<?php
/**
 * Ends the session for any role.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

logout_user();

// The flash message needs a fresh session.
session_start();
set_flash('success', 'You have been logged out.');
redirect('/index.php');
