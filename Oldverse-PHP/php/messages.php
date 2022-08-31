<?php
require_once "lib/connect.php";
requireAuth();
$user = getUser($_SESSION['token']);
if(!isset($_GET['id'])){
    header("Location: /");
}
$stmt = $db->prepare("SELECT id, nickname FROM users WHERE username = ?");
$stmt->bind_param('s', $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows==0){
    header("Location: /");
}
$row = $result->fetch_assoc();
if(isset($_SESSION['token']) && $user['username'] !== $_GET['id']){
    $stmt = $db->prepare("SELECT id FROM follows WHERE source = ? AND target = ?");
    $stmt->bind_param('ii', $user['id'], $row['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_follow = $result->num_rows==0 ? false : true;
    $stmt = $db->prepare("SELECT id FROM follows WHERE source = ? AND target = ?");
    $stmt->bind_param('ii', $row['id'], $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_following_back = $result->num_rows==0 ? false : true;
}else{
    header("Location : /");
}
if(!$is_following_back || !$is_follow){
    header("Location: /");
}
$stmt = $db->prepare("SELECT cid FROM conversations WHERE source = ? AND target = ?");
$stmt->bind_param('ii', $user['id'], $row['id']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows==0 && $_SERVER['REQUEST_METHOD']=='POST'){
    showJSONError(400, 6556415, "You don't have any DM open with this user yet.");
}
if($result->num_rows==0){
    $stmt = $db->prepare("SELECT cid FROM conversations ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows==0){
        $cid = 1;
    }
    if(!isset($cid)){
        $crow = $result->fetch_assoc();
        $cid = $crow['cid'] + 1;
    }
    $stmtcreation = $db->prepare("INSERT INTO conversations(source, target, cid) VALUES(?, ?, ?)");
    $stmtcreation->bind_param('iii', $user['id'], $row['id'], $cid);
    $stmtcreation->execute();
    $stmtcreation = $db->prepare("INSERT INTO conversations(source, target, cid) VALUES(?, ?, ?)");
    $stmtcreation->bind_param('iii', $row['id'], $user['id'], $cid);
    $stmtcreation->execute();
    header("Location: /messages/".$_GET['id']);
}
$crow = $result->fetch_assoc();
if($_SERVER['REQUEST_METHOD']=="POST"){
    //YAY JSON API endpoint + user interface aio lmao
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
    $stmt = $db->prepare("INSERT INTO dms(author, target, cid, feeling, body) VALUES(?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $user['id'], $row['id'], $crow['cid'], $_POST['feeling_id'], $_POST['body']);
    $stmt->execute();
    if($stmt->error){
        showJSONError(500, 5626485, "There was an error inserting your DM in the database.");
    }
    $stmt = $db->prepare("SELECT dms.id, author, feeling, body, (SELECT UNIX_TIMESTAMP(created_at)) AS timestamp, level, mii_hash, nickname FROM dms LEFT JOIN users on users.id = author WHERE cid = ? AND author = ? ORDER BY dms.id DESC LIMIT 1");
    $stmt->bind_param('ii', $crow['cid'], $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows==0){
        showJSONError(500, 5216695, "WTF message not found?!");
    }
    $row = $result->fetch_assoc();
    require "elements/message.php";
    exit;
}
$stmt = $db->prepare("UPDATE dms SET is_read = 1 WHERE cid = ? AND target = ?");
$stmt->bind_param('ii', $crow['cid'], $user['id']);
$stmt->execute();
$title = "Message ".$row['nickname'];
require_once "lib/header.php";
$_GET['offset'] = !empty($_GET['offset']) ? $_GET['offset'] : 0;

?>
<h2 class="headline">Message <?= htmlspecialchars($row['nickname']) ?></h2>
<form id="post-form" method="post" action="/messages/palataco" class="folded for-identified-users">
  
  
  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>
  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="Share your thoughts in a post to <?= htmlspecialchars($row['nickname']) ?>." data-open-folded-form="" data-required=""></textarea>
  
  <div class="form-buttons">
    <input type="submit" class="black-button post-button" value="Send">
  </div>
</form>
<?
$stmt = $db->prepare("SELECT dms.id, author, feeling, body, created_at, (SELECT UNIX_TIMESTAMP(dms.created_at)) AS timestamp, level, mii_hash, nickname, username FROM dms LEFT JOIN users on users.id = author WHERE cid = ? ORDER BY dms.id DESC LIMIT 20 OFFSET ?");
$stmt->bind_param('ii', $crow['cid'], $_GET['offset']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows==0){
    showNoContent("No posts were found on this conversation.");
}else{
    $new_offset = $_GET['offset'] + 20;?>
    <div class="list post-list" data-next-page-url="/messages/<?= $_GET['id'] ?>?offset=<?= $new_offset ?>"><?
    while($row = $result->fetch_array()){
        require "elements/message.php";
    }
    ?></div><?
}
?>