<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['user'];
    $password = $_POST['password'];

    $user = $users->findOne(['user' => $userName]);

    if ($user && $user['password'] === $password) {
        $_SESSION['user'] = $userName;
        header('location: index.php');
    } else {
        echo "<script>
                    alert('Identifiant ou mot de passe incorrect');
                    window.location.href = 'x.php';
              </script>";
    }
}