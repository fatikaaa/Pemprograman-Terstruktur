<?php
// FILE: user/logout.php
require_once '../config.php';
unset($_SESSION['user_id']);
unset($_SESSION['user_nama']);
header('Location: login.php');
exit;
?>