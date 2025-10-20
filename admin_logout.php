<?php
require_once 'classes/AdminSessionManager.php';

AdminSessionManager::start();
AdminSessionManager::logout();

header('Location: admin_login.php');
exit();
?>
