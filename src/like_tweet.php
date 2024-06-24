<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tweetId = $_GET['id'];
}

$collection->updateOne(
    ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
    ['$inc' => ['likes' => 1]]
);
header('location:index.php');