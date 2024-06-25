<?php
require 'config.php';
session_start();

$tweetId = $_GET['id'];

// verif que l'id du tweet est fourni
if (!$tweetId) {
    header('location:index.php');
    exit;
}

$tweet = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($tweetId)]);
$currentUser = $_SESSION['user'];
$userRole = $users->findOne(['user' => $currentUser]);
$currentUserRole = $userRole['role'];

// Verif pour savoir si il s'agit de l'auteur du tweet ou d'un moderator
if ($tweet && ($userRole['role'] == 'moderator' || $_SESSION['user'] == $tweet['user'])) {
    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($tweetId)]);
}
header('location:index.php');
exit;