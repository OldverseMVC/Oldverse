<?php
require_once "lib/connect.php";
if($_SERVER['REQUEST_METHOD']!=="POST"){
    showJSONError(403, 403403, "You must use a POST request.");
}
if(!isset($_SESSION['token'])){
    showJSONError(403, 5845545, 'You must be logged in to perform this action.');
}
if(empty($_POST['body']) || !isset($_POST['feeling_id'])){
    showJSONError(400, 400000, "Your body/feeling is empty.");
}
if(empty($_POST['feeling_id']) || $_POST['feeling_id'] > 5 || $_POST['feeling_id'] < 0) {
    $_POST['feeling_id'] = 0;
}
$_POST['body'] = trim($_POST['body']);
if(mb_strlen($_POST['body']) > 2000) {
    showJSONError(400, 1219309, 'Your body is too long.');
}
$user = getUser($_SESSION['token']);
$stmt = $db->prepare("SELECT users.id, created_by, is_locked FROM posts LEFT JOIN users ON created_by = users.id WHERE posts.id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5654548, 'There was an error while checking the post existence.');
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(404, 5475498, "The post doesn't exist.");
}
if(empty($_POST['is_spoiler'])) {
    $_POST['is_spoiler'] = 0;
}
if(empty($_POST['screenshot'])){
    $_POST['screenshot'] = null;
}
if(!empty($_POST['screenshot']) && !preg_match('|(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', $_POST['screenshot'])){
     showJSONError(400, 584694, 'Invalid screenshot URL.');
}
$exts = array('jpg', 'gif', 'png', 'webp', 'jpeg', 'bmp', 'svg');

if(!empty($_POST['screenshot']) && !in_array(strtolower(pathinfo($_POST['screenshot'], PATHINFO_EXTENSION)), $exts)) {
    showJSONError(400, 5154487, "Your screenshot URL is NOT an image. (or it is an data:image)");
}
if(!empty($_POST['screenshot'])){
    $chk = file_get_contents($_POST['screenshot']);
    if(!$chk){
        showJSONError(400, 6515258, "It seems your screenshot doesn't exist.");
    }
}
$post_row = $result->fetch_array();
if($post_row['is_locked']==1){
    showJSONError(400, 5256516, "This post has been locked and is not open for further comments.");
}
if(preg_match('|@([a-zA-Z0-9_-]{2,50})|', $_POST['body'], $matches)){
    foreach($matches as $match){
        $match = preg_replace('|@([a-zA-Z0-9_-]{2,50})|', '$1', $match);
        $stmt = $db->prepare("SELECT id FROM `users` WHERE username = ?");
        $stmt->bind_param("s", $match);
        $stmt->execute();
        $result = $stmt->get_result();
        $row2 = $result->fetch_assoc();
        if(empty($row2)) {
            showJSONError(400, 9856457, 'One of the pinged user does not exist.');
        }
        if($stmt->error){
            showJSONError(500, 5820194, 'There was an error while trying to get pinged user.');
        }
    }
}
$stmt = $db->prepare('SELECT COUNT(*) FROM replies WHERE created_by = ? AND created_at > NOW() - INTERVAL '.rand(15,20).' SECOND');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 5820194, 'There was an error while grabbing your recent replies.');
}
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if($row['COUNT(*)'] > 0) {
    showJSONError(403, 1213005, 'You\'re making too many posts in quick succession. Please try again in a moment.');
}
$stmt = $db->prepare("INSERT INTO `replies`(created_by, target, body, spoiler, feeling, screenshot) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iisiis', $user['id'], $_GET['id'], $_POST['body'], $_POST['is_spoiler'], $_POST['feeling_id'], $_POST['screenshot']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5655255, "An error occured while inserting the reply in the database.");
}
$stmt = $db->prepare("SELECT replies.id, created_by, body, screenshot, spoiler, feeling, created_at, mii_hash, username, nick_color, nickname, level, (SELECT COUNT(*) FROM empathies WHERE target = replies.id AND type = 1) AS empathy_count,  (SELECT UNIX_TIMESTAMP(replies.created_at)) AS timestamp FROM replies LEFT JOIN users ON created_by = users.id WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5652656, 'An error occured while fetching your reply from the database.');
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(500, 4754858, 'WTF reply not found?!');
}
$row = $result->fetch_array();
if($user['id']!==$post_row['id']){
    if(!sendNotif($user['id'], $post_row['id'], 2, "/posts/".$_GET['id'], $_GET['id'])){
        showJSONError(500, 5192669, 'An error occured while sending a notification. (your reply has been posted)');
    }
}
if(preg_match('|@([a-zA-Z0-9_-]{2,50})|', $_POST['body'], $matches)){
    foreach($matches as $match){
        if(!sendNotif($user['id'], $row2['id'], 6, "/replies/".$_GET['id'], $_GET['id'])){
            showJSONError(500, 1726354, 'An error occured while sending a notification to pinged users (post has been made).');
        }
    }
}
require "elements/reply.php";
?>
