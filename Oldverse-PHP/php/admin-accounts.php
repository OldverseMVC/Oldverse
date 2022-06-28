<?php
require_once "lib/adm-header.php";
if($user["level"] < 1 || !isset($_SESSION['token'])){
    exit("Access_Denied");
}
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(!isset($_POST['mode'])){
        $error =  'You must precise a mode.';
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
            $error =  'oh no, SOME FIELDS ARE EMPTY <:DDDDD';
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
            $error =  'oh no, SOME FIELDS ARE EMPTY <:DDDDD';
        }
    }
    if($_POST['mode']=="removepost"){
        if(!empty($_POST['postid'])){
            $stmt = $db->prepare("DELETE FROM `posts` WHERE id = ?");
            $stmt->bind_param('i', $_POST['postid']);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM `replies` WHERE target=?");
            $stmt->bind_param('i', $_POST['postid']);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM `news` WHERE additional_id=? AND type = 1 OR additional_id=? AND type = 2");
            $stmt->bind_param('ii', $_POST['postid'], $_POST['postid']);
            $stmt->execute();
            $success_msg = "This post got removed!";
            goto showForm;
        }else{
            //got bored
            $error =  'oh no, SOME FIELDS ARE EMPTY <:DDDDD';
        }
    }
    if($_POST['mode']=="purgeact"){
        if(!empty($_POST['userid'])){
            $stmt = $db->prepare("DELETE FROM `posts` WHERE created_by = ?");
            $stmt->bind_param('i', $_POST['userid']);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM `replies` WHERE created_by=?");
            $stmt->bind_param('i', $_POST['userid']);
            $stmt->execute();
            $success_msg = "This user's posts got purged!";
            goto showForm;
        }else{
            //got bored
            $error =  'oh no, SOME FIELDS ARE EMPTY <:DDDDD';
        }
    }
}
showForm:
?>
<div class="mainContainer">
    <div class="col-md-6 col-md-offset-3">
        <h1>Account/posts management</h1>
        <?
        if(isset($success_msg)){?>
        <p class="green"><?= $success_msg ?></p>
        <? } ?>
        <?
        if(isset($error)){?>
        <p class="red"><?= $error ?></p>
        <? } ?>
        <h2>Ban accounts</h2>
        <p>Banning is a serious action, it can be destructive.</p>
        <p style="color: red;"><b>Think 2-3 more times before banning someone!</b></p>
        <form method="post">
            <input type="hidden" name="mode" value="ban">
            <input type="number" name="userid" placeholder="User ID" maxlength="255">
            <br>
            <br>
            <input type="text" name="reason" placeholder="Reason" maxlength="255">
            <br>
            <br>
            <input type="submit" class="btn btn-success" value="GO!">
        </form>
        <h2>Unban accounts</h2>
        <p>Unbanning is a serious action, it can be destructive.</p>
        <p style="color: red;"><b>Think 2-3 more times before unbanning someone!</b></p>
        <form method="post">
            <input type="hidden" name="mode" value="unban">
            <input type="number"  name="userid" placeholder="User ID" maxlength="255">
            <br>
            <br>
            <input type="submit" class="btn btn-success" value="GO!">
        </form>
        <h2 class="label">Remove post</h1>
        <form method="post">
            <input type="hidden" name="mode" value="removepost">
            <input type="number" name="postid" placeholder="Post ID" maxlength="255">
            <br>
            <br>
            <input type="submit" class="btn btn-success" value="GO!">
        </form>
        <h1>Purge posts from User ID</h1>
        <p style="color: red;"><b>/!!\ THIS IS A DESTRUCTIVE ACTION /!!\</b></p>
        <form method="post">
            <input type="hidden" name="mode" value="purgeact">
            <input type="number" name="userid" placeholder="User ID" maxlength="255">
            <br>
            <br>
            <input type="submit" class="btn btn-success" value="GO!">
        </form>
    </div>
</div>