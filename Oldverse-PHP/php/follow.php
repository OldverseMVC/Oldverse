<?php
require_once "lib/connect.php";
if(!isset($_SESSION['username']) || !isset($_SESSION['token'])){
    showJSONError(403, 5658656, "You must be logged in to perform this action.");
}
if(!isset($_GET['id'])){
    showJSONError(400, 4552525, "You must precise a user ID.");
}
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param('s', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 4555458, "An error occured while checking if the user exists.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(404, 5564688, "This user doesn't exist.");
}
$row = $result->fetch_array();
$user = getUser($_SESSION['token']);
if($row['id']==$user['id']){
    showJSONError(403, 5284659, "You cannot follow yourself.");
}
$stmt = $db->prepare("SELECT * FROM follows WHERE `source`=? AND `target`=?");
$stmt->bind_param('ii', $user['id'], $row['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5255545, "An error occured while checking for existing follows.");
}
$result = $stmt->get_result();
if($result->num_rows != 0){
    $is_follow = true;
}else{
    $is_follow = false;
}
if($is_follow){
    $stmt = $db->prepare("DELETE FROM follows WHERE source = ? AND target = ?");
}else{
    $stmt = $db->prepare("INSERT INTO follows(source, target) VALUES(?, ?)");
}
$stmt->bind_param('ii', $user['id'], $row['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5654956, "An error occured while inserting/deleting the follow.");
}
if(!$is_follow){
    if(!sendNotif($user['id'], $row['id'], 4, '/users/'.$user['username'])){
        showJSONError(500, 5282694, "Could not send notification (but your follow has been made).");
    }
 }