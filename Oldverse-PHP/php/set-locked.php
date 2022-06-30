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
$stmt = $db->prepare("SELECT (SELECT owner FROM communities WHERE id = posts.community) AS owner FROM posts WHERE id = ?");
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
if($row['owner'] !== $user['id']){
    if($user['level'] < 1){
        showJSONError(403, 6265654, "You can't perform this action.");
    }
}
$stmt = $db->prepare("UPDATE `posts` SET `is_locked`=1 WHERE id=?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 6655854, 'An error occured while trying to update your post.');
}