<?php
require_once "lib/connect.php";
if(!isset($_SESSION['username']) || !isset($_SESSION['token'])){
    showJSONError(403, 5658656, "You must be logged in to perform this action.");
}
if(!isset($_GET['id'])){
    showJSONError(400, 4552525, "You must precise a user ID.");
}
$stmt = $db->prepare("SELECT id FROM communities WHERE id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 4555458, "An error occured while checking if the community exists.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(404, 5564688, "This community doesn't exist.");
}
$row = $result->fetch_array();
$user = getUser($_SESSION['token']);
$stmt = $db->prepare("SELECT id FROM favorites WHERE `source`= ? AND `target`=?");
$stmt->bind_param('ii', $user['id'], $row['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5255545, "An error occured while checking for existing favorites communities.");
}
$result = $stmt->get_result();
if($result->num_rows != 0){
    $is_favorite = true;
}else{
    $is_favorite = false;
}
if($is_favorite){
    $stmt = $db->prepare("DELETE FROM favorites WHERE source = ? AND target = ?");
}else{
    $stmt = $db->prepare("INSERT INTO favorites(source, target) VALUES(?, ?)");
}
$stmt->bind_param('ii', $user['id'], $row['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5654956, "An error occured while inserting/deleting the favorite.");
}