<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tweetId = $_GET['id'];
    $commentIndex = (int)$_POST['comment_index'];  // Utilisation de l'index du tableau
}

$collection->updateOne(
    [
        '_id' => new MongoDB\BSON\ObjectId($tweetId),
    ],
    [
        '$inc' => ["comments.$commentIndex.likes" => 1]
    ]
);
header('location:index.php');