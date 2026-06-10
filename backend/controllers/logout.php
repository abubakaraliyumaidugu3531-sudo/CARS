<?php
require_once "../helpers/session.php";

logout();

// Redirect to login
header("Location: ../../frontend/pages/login.php");
exit();
?>