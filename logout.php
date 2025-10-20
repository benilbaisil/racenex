<?php
require_once 'classes/SessionManager.php';

SessionManager::start();
SessionManager::logout();
header('Location: index.php');
exit;
