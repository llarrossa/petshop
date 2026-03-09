<?php
/**
 * Landing Page - Redireciona para login ou sistema
 */

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
} else {
    header('Location: public/login.php');
}
exit;
