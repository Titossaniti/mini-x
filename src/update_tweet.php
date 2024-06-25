<?php
require 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tweetId = $_GET['id'];
    $newMessage = $_POST['message'];
    $tweet = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($tweetId)]);

    // Vérifiez si l'utilisateur connecté est l'utilisateur qui a posté le tweet
    if ($_SESSION['user'] == $tweet['user']) {
        $collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
            ['$set' => ['message' => $newMessage]]
        );
    }
}
header('location:index.php');
