<?php
require_once "lib/connect.php";
if(empty($_SESSION['token'])){
    header("Location: /");
}
$user = getUser($_SESSION['token']);
header("Location: /users/".$user['username']);