<?php
require_once "lib/connect.php";
if(!isset($_SESSION['username']) || !isset($_SESSION['token'])){
    showJSONError(403, 5658656, "You must be logged in to perform this action.");
}
$verbose_type = $_GET['type'] == 1 ? 'reply' : 'post';
if(!isset($_GET['id'])){
    showJSONError(400, 4552525, "You must precise a ".$verbose_type." ID.");
}
if($_GET['type'] > 1 || $_GET['type'] < 0) {
    showJSONError(400, 5545286, $_GET['type']);
}
if($_GET['type']==0){
    $stmt = $db->prepare("SELECT created_by FROM posts WHERE id = ?");
}else{
    $stmt = $db->prepare("SELECT created_by FROM replies WHERE id = ?");
}
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 4555458, "An error occured while checking if the ".$verbose_type." exists.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(404, 5564688, "This ".$verbose_type." doesn't exist.");
}
$row = $result->fetch_array();
$user = getUser($_SESSION['token']);
if($row['created_by']==$user['id']){
    showJSONError(403, 5284659, "You cannot yeah your own ".$verbose_type.".");
}
$stmt = $db->prepare("SELECT * FROM empathies WHERE `source`=? AND `target`=? AND type = ?");
$stmt->bind_param('iii', $user['id'], $_GET['id'], $_GET['type']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5255545, "An error occured while checking for existing yeahs.");
}
$result = $stmt->get_result();
if($result->num_rows != 0){
    $is_yeah = true;
}else{
    $is_yeah = false;
}
if($is_yeah){
    $stmt = $db->prepare("DELETE FROM empathies WHERE source = ? AND target = ? AND type= ?");
}else{
    $stmt = $db->prepare("INSERT INTO empathies(source, target, type) VALUES(?, ?, ?)");
}
$stmt->bind_param('iii', $user['id'], $_GET['id'], $_GET['type']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5654956, "An error occured while inserting/deleting the yeah.");
}
if(!$is_yeah){
    $notif_type = $_GET['type'] == 1 ? 3 : 1;
    $verbose_type = $_GET['type'] == 1 ? 'replies' : 'posts';
    if(!sendNotif($user['id'], $row['created_by'], $notif_type, '/'.$verbose_type.'/'.$_GET['id'], $_GET['id'])){
        showJSONError("An error occured while sending the notification. (but your yeah has been made)");
    }
}