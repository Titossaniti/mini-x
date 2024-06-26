<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['user'];
    $password = $_POST['password'];}

$users->insertOne([
    'user' => $userName,
    'password' => $password
]);
header('location:index.php');