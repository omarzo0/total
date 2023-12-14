<?php
session_start();

// Function to delete the login cookie
function deleteLoginCookie() {
    if (isset($_COOKIE['login'])) {
        setcookie('login', '', time() - 3600, '/');
    }
}

// Check if the user is logged in
if (isset($_SESSION['id'])) {
    // Unset the session variable
    unset($_SESSION['id']);
    unset($_SESSION['name']);
    
    // Delete the login cookie
    deleteLoginCookie();
}

// Redirect to the login page
header("Location: index.php");
exit();
?>
