<?
ob_start();
require_once "lib/header.php";
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
?>
<h1>Send flipnote</h1>
<p>You're almost there!</p>
<a href="post_4.php?token=<?= $_GET['token'] ?>&feeling=<?= $_GET['feeling'] ?>" hreftype="post_kwz(UseLockWindow,CameraNg)">Click to choose the flipnote</a>
<?
error:
if(isset($error)){?><p style="color:red;"><?= $error ?></p><? }?>
</body>
</html>
<?
header('Content-Length: '.ob_get_length());
ob_end_flush();
?>