<?php
require 'config.php';
session_start();

// Vider $_session
$_SESSION = [];

session_destroy();

// Redirection
header('Location: index.php');
exit();