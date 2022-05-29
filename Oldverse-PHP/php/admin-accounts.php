<?php
$title = "Admin Panel";
require_once "lib/header.php";
requireAuth();
if($user["level"] < 1){
    showError(403, "You're not allowed to access this page.");
}
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(!isset($_POST['mode'])){
        showError(400, 'You must precise a mode.');
    }
    if($_POST['mode']=="ban"){
        if(!empty($_POST['userid']) && !empty($_POST['reason'])){
            $stmt = $db->prepare("INSERT INTO `ban`(target, reason) VALUES(?, ?)");
            $stmt->bind_param('is', $_POST['userid'], $_POST['reason']);
            $stmt->execute();
            $success_msg = "This user got banned!";
            goto showForm;
        }else{
            //got bored
            showError(400, 'oh no, SOME FIELDS ARE EMPTY <:DDDDD');
        }
    }
    if($_POST['mode']=="unban"){
        if(!empty($_POST['userid'])){
            $stmt = $db->prepare("DELETE FROM `ban` WHERE target = ?");
            $stmt->bind_param('i', $_POST['userid']);
            $stmt->execute();
            $success_msg = "This user got unbanned!";
            goto showForm;
        }else{
            //got bored
            showError(400, 'oh no, SOME FIELDS ARE EMPTY <:DDDDD');
        }
    }
    if($_POST['mode']=="removepost"){
        if(!empty($_POST['postid'])){
            $stmt = $db->prepare("DELETE FROM `posts` WHERE id = ?");
            $stmt->bind_param('i', $_POST['postid']);
            $stmt->execute();
            $success_msg = "This post got removed!";
            goto showForm;
        }else{
            //got bored
            showError(400, 'oh no, SOME FIELDS ARE EMPTY <:DDDDD');
        }
    }
    if($_POST['mode']=="purgeact"){
        if(!empty($_POST['userid'])){
            $stmt = $db->prepare("DELETE FROM `posts` WHERE created_by = ?");
            $stmt->bind_param('i', $_POST['userid']);
            $stmt->execute();
            $success_msg = "This user's posts got purged!";
            goto showForm;
        }else{
            //got bored
            showError(400, 'oh no, SOME FIELDS ARE EMPTY <:DDDDD');
        }
    }
}
showForm:
?>
<h1 class="headline">Admin Panel - Manage accounts/posts</h1>
<div class="center">
    <?
    if(isset($success_msg)){?>
    <br>
    <p style="color: green;"><?= $success_msg ?></p>
    <br>
    <? } ?>
    <h2 class="label">Ban accounts</h1>
    <p>Banning is a serious action, it can be destructive.</p>
    <p style="color: red;"><b>Think 2-3 more times before banning someone!</b></p>
    <form id="post-form" method="post" class="for-identified-users">
        <input type="hidden" name="mode" value="ban">
        <input type="number" class="textarea-line url-form" name="userid" placeholder="User ID" maxlength="255">
        <br>
        <br>
        <input type="text" class="textarea-line url-form" name="reason" placeholder="Reason" maxlength="255">
        <div class="form-buttons">
            <input type="submit" class="black-button post-button" value="GO!">
        </div>
    </form>
    <h2 class="label">Unban accounts</h1>
    <p>Unbanning is a serious action, it can be destructive.</p>
    <p style="color: red;"><b>Think 2-3 more times before unbanning someone!</b></p>
    <form id="post-form" method="post" class="for-identified-users">
        <input type="hidden" name="mode" value="unban">
        <input type="number" class="textarea-line url-form" name="userid" placeholder="User ID" maxlength="255">
        <div class="form-buttons">
            <input type="submit" class="black-button post-button" value="GO!">
        </div>
    </form>
    <h2 class="label">Remove post</h1>
    <form id="post-form" method="post" class="for-identified-users">
        <input type="hidden" name="mode" value="removepost">
        <input type="number" class="textarea-line url-form" name="postid" placeholder="Post ID" maxlength="255">
        <div class="form-buttons">
            <input type="submit" class="black-button post-button" value="GO!">
        </div>
    </form>
    <h2 class="label">Purge posts from User ID</h1>
    <p style="color: red;"><b>/!!\ THIS IS A DESTRUCTIVE ACTION /!!\</b></p>
    <form id="post-form" method="post" class="for-identified-users">
        <input type="hidden" name="mode" value="purgeact">
        <input type="number" class="textarea-line url-form" name="userid" placeholder="User ID" maxlength="255">
        <div class="form-buttons">
            <input type="submit" class="black-button post-button" value="GO!">
        </div>
    </form>
</div>