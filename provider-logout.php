<?php
session_start();
unset($_SESSION['provider_id']);
unset($_SESSION['provider_name']);
unset($_SESSION['provider_email']);
session_destroy();
header("Location: provider-login.php");
exit();
?>
