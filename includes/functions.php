<?php
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
