<?php
require 'config.php';


$tweetId = $_GET['id'];

$collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($tweetId)]);

header('location:index.php');