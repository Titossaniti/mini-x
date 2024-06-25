<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tweetId = $_GET['id'];
    $commentMessage = $_POST['message'];

    // S'assurer que l'utilisateur est connecté
    if (isset($_SESSION['user'])) {
        $currentUser = $_SESSION['user'];


        $collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
            ['$push' => ['comments' => [
                'user' => $currentUser,
                'message' => $commentMessage,
                'timestamp' => new MongoDB\BSON\UTCDateTime()
            ]]]
        );
    } else {
        echo "<script>
                    alert('Vous devez être connecté pour pouvoir poster un tweet');
                    window.location.href = 'x.php';
              </script>";
        exit;
    }
}
header('location:index.php');