<?php
require 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];}

$collection->insertOne([
    'user' => $currentUser,
    'message' => $message,
    'timestamp' => new MongoDB\BSON\UTCDateTime()
]);
header('location:index.php');