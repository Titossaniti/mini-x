<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    // S'assurer que l'utilisateur est connecté
    if (isset($_SESSION['user'])) {
        $currentUser = $_SESSION['user'];

        $collection->insertOne([
            'user' => $currentUser,
            'message' => $message,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ]);
    } else {
        echo "<script>
                    alert('Vous devez être connecté pour pouvoir poster un Yeet');
                    window.location.href = 'x.php';
              </script>";
        exit;
    }
}
header('location:index.php');