<?php
require_once "lib/connect.php";
if($_SERVER['REQUEST_METHOD']!=="POST"){
    showJSONError(403, 4034003, "You must use a POST request.");
}
if(!isset($_SESSION['token'])){
    showJSONError(403, 5845545, 'You must be logged in to perform this action.');
}
if(!isset($_GET['id'])){
    showJSONError(403, 8484888, 'You must precise a post ID.');
}
$stmt = $db->prepare("SELECT created_by FROM posts WHERE id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 6565955, 'An error occured while fetching the post from the database.');
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(400, 2656558, 'Post not found.');
}
$row = $result->fetch_array();
$user = getUser($_SESSION['token']);
if($user['id']!==$row['created_by']){
    showJSONError(403, 6265654, "You're not the post owner.");
}
$stmt = $db->prepare("DELETE FROM `posts` WHERE id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 6655854, 'An error occured while trying to remove the post.');
}
$stmt = $db->prepare("DELETE FROM `replies` WHERE target=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 6655854, 'An error occured while trying to remove the comments.');
}
$stmt = $db->prepare("DELETE FROM `news` WHERE additional_id=? AND type = 1 OR additional_id=? AND type = 2");
$stmt->bind_param('ii', $_GET['id'], $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 6655854, 'An error occured while trying to remove the notifications.');
}