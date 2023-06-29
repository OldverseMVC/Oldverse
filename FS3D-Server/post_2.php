<?php
ob_start();
require_once "lib/header.php";
$stmt = $db->prepare("SELECT id FROM `users` WHERE flipnote_token = ?");
$stmt->bind_param('i', $_POST['text']);
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
?>
<h1>Select feeling</h1>
<a href="post_3.php?feeling=0&token=<?= $_POST['text'] ?>">Normal</a><br><a href="post_3.php?feeling=1&token=<?= $_POST['text'] ?>">Happy</a><br><a href="post_3.php?feeling=2&token=<?= $_POST['text'] ?>">Wink</a><br><a href="post_3.php?feeling=3&token=<?= $_POST['text'] ?>">Surprised</a><br><a href="post_3.php?feeling=4&token=<?= $_POST['text'] ?>">Scared</a><br><a href="post_3.php?feeling=5&token=<?= $_POST['text'] ?>">Sad</a>
<?
error:
if(isset($error)){?><p style="color:red;"><?= $error ?></p><?php }?>
</body>
</html>
<?php
header('Content-Length: '.ob_get_length());
ob_end_flush();
?>
