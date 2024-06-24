<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tweetId = $_GET['id'];
    $newMessage = $_POST['message'];
}

$collection->updateOne(
    ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
    ['$set' => ['message' => $newMessage]]
);
header('location:index.php');