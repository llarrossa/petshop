<?php
/**
 * Logout do Sistema
 */

require_once __DIR__ . '/../config/config.php';

session_destroy();

header('Location: ' . APP_URL . '/public/login.php');
exit;
