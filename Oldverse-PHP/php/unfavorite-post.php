<?php
require_once "lib/connect.php";
if($_SERVER['REQUEST_METHOD']!=="POST"){
    showJSONError(403, 4034003, "You must use a POST request.");
}
if(!isset($_SESSION['token'])){
    showJSONError(403, 5845545, 'You must be logged in to perform this action.');
}
$user = getUser($_SESSION['token']);
$stmt = $db->prepare("UPDATE `users` SET `favorite`=null WHERE id=?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 6655854, 'An error occured while trying to update your user.');
}