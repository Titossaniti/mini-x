<?php
require '../vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->mini_x->tweets;
$users = $client->mini_x->users;