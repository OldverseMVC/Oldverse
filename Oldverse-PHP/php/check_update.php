<?php
require_once "lib/connect.php";
header('Content-Type: application/json; charset=utf-8');
if(empty($_SESSION['token'])){
    $notifs = array(
        "success" => 1,
        "admin_message" => array('unread_count' => 0),
        "mission" => array('unread_count' => 0),
        "news" => array('unread_count' => 0),
    );
    exit(json_encode($notifs));
}
$user = getUser($_SESSION['token']);
$stmt = $db->prepare("SELECT COUNT(*) FROM news WHERE target = ? AND is_read = 0");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 548488, "An error occured while checking notifications.");
}
$result = $stmt->get_result();
$row = $result->fetch_array();
//build notifs array
$notifs = array(
    "success" => 1,
    "admin_message" => array('unread_count' => 0),
    "mission" => array('unread_count' => 0),
    "news" => array('unread_count' => $row['COUNT(*)']),
);
$stmt = $db->prepare("UPDATE users SET last_online=CURRENT_TIMESTAMP WHERE id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 252566, "An error occured while updating your online status.");
}
echo json_encode($notifs);