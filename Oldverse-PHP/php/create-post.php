<?php
require_once "lib/connect.php";
if($_SERVER['REQUEST_METHOD']!=="POST"){
    showJSONError(403, 403403, "You must use a POST request.");
}
if(!isset($_SESSION['token'])){
    showJSONError(403, 5845545, 'You must be logged in to perform this action.');
}
if(empty($_POST['community']) && !isset($_GET['activity']) || empty($_POST['body']) || !isset($_POST['feeling_id'])){
    showJSONError(400, 400000, "Your body/feeling/community is empty.");
}
if(empty($_POST['feeling_id']) || $_POST['feeling_id'] > 5 || $_POST['feeling_id'] < 0) {
    $_POST['feeling_id'] = 0;
}
$_POST['body'] = trim($_POST['body']);
if(mb_strlen($_POST['body']) > 2000) {
    showJSONError(400, 1219309, 'Your body is too long.');
}
$user = getUser($_SESSION['token']);
if(empty($_POST['community'])) {
    $_POST['community'] = 0;
}else{
    $stmt = $db->prepare("SELECT id, permissions FROM communities WHERE id = ?");
    $stmt->bind_param('i', $_POST['community']);
    $stmt->execute();
    if($stmt->error){
        showJSONError(500, 8754872, 'An error occured while grabbing the community.');
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        showJSONError(404, 7578486, 'This community does not exist.');
    }
    $crow = $result->fetch_array();
    if($user['level'] < $crow['permissions']){
        showJSONError(404, 7578486, "You don't have permission to post inside this community.");
    }
}
if(empty($_POST['is_spoiler'])) {
    $_POST['is_spoiler'] = 0;
}
$screenshot = null;
$stmt = $db->prepare('SELECT COUNT(*) FROM posts WHERE created_by = ? AND created_at > NOW() - INTERVAL '.rand(15,20).' SECOND');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error) {
    showJSONError(500, 5820194, 'There was an error while grabbing your recent posts.');
}
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if($row['COUNT(*)'] > 0) {
    showJSONError(403, 1213005, 'You\'re making too many posts in quick succession. Please try again in a moment.');
}
$stmt = $db->prepare("INSERT INTO `posts`(community, created_by, body, url, screenshot, spoiler, feeling) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iisssii', $_POST['community'], $user['id'], $_POST['body'], $_POST['url'], $screenshot, $_POST['is_spoiler'], $_POST['feeling_id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5655255, "An error occured while inserting the post in the database.");
}
$stmt = $db->prepare("SELECT posts.id, community, created_by, body, url, screenshot, spoiler, feeling, created_at, mii_hash, username, nickname, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count,  (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 5652656, 'An error occured while fetching your post from the database.');
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showJSONError(500, 4754858, 'WTF Post not found?!');
}
$row = $result->fetch_array();
require "elements/post.php";
?>