<?php
session_start();
function is_logged_in()
{
    return isset($_SESSION['id']);
}

if (!is_logged_in() || $_SESSION['id'] == 0) {
    echo '<script>alert("يجب تسجيل الدخول اولآ"); window.location.href = "index.php";</script>';
    exit;
}
?>