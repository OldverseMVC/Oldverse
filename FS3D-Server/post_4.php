<?php
require_once "lib/connect.php";
require_once "lib/KwzParser.php";
ob_start();
$stmt = $db->prepare("SELECT id FROM `users` WHERE flipnote_token = ?");
$stmt->bind_param('i', $_GET['token']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_array();
if($result->num_rows==0){
    $error = "Incorrect flipnote token.";
    goto error;
}
$stmt = $db->prepare("SELECT id FROM `ban` WHERE target = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows>0){
    $error = "You have been banned.";
    goto error;
}
if($_GET['feeling'] < 0 || $_GET['feeling'] > 5){
    $error = "Invalid feeling ID.";
    goto error;
}
if(empty($_POST['formFile'])){
    $error = "Flipnote uploading failed: file not found.";
    goto error;
}
$kwz = new KwzParser($_POST['formFile']);
$jpeg = $kwz->getSectionData("KTN");
$flipnote_id = rand(1, 2147483647);
file_put_contents(OLDVERSE_INSTANCE_PATH.'/flipnote/thumb/'.$flipnote_id.'.jpg', $jpeg);
file_put_contents(OLDVERSE_INSTANCE_PATH.'/flipnote/kwz/'.$flipnote_id.'.kwz', $_POST['formFile']);
$stmt = $db->prepare("SELECT id FROM communities WHERE is_flipnote = 1");
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows==0){
    $community = 0;
}else{
    $crow = $result->fetch_array();
    $community = $crow['id'];
}
$zero = 0;
$null = null;
$text = 'My Flipnote';
$stmt = $db->prepare("INSERT INTO `posts`(community, created_by, body, url, screenshot, spoiler, feeling, flipnote) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iisssiis', $community, $user['id'], $text, $null, $null, $zero, $_GET['feeling'], $flipnote_id);
$stmt->execute();
header("Location: http://".$_SERVER['SERVER_NAME']."/finish.php");
error:
if(isset($error)){?><html><body><p style="color:red;"><?= $error ?></p></body></html><?php }
header('Content-Length: '.ob_get_length());
ob_end_flush();
?>
