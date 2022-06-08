<?php
require_once "lib/connect.php";
if(!isset($_GET['id'])){
    showError(400, 'You must precise an user ID.');
}
$stmt = $db->prepare("SELECT id, nickname, mii_hash, description, created_on, level, nnid, url, (SELECT COUNT(*) FROM posts WHERE created_by = users.id) AS post_num, (SELECT COUNT(*) FROM follows WHERE target = users.id) AS follow_num, (SELECT COUNT(*) FROM follows WHERE source = users.id) AS followed_num FROM users WHERE username = ?");
$stmt->bind_param('s', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showError(500, 'An error occured while fetching the user from the database.');
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showError(404, 'The user could not be found.');
}
$row = $result->fetch_array();
$title = $row['nickname']."'s profile";
require_once "lib/header.php";
if(isset($_SESSION['token']) && $user['id'] !== $row['id']){
    $stmt = $db->prepare("SELECT id FROM follows WHERE source = ? AND target = ?");
    $stmt->bind_param('ii', $user['id'], $row['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_follow = $result->num_rows==0 ? false : true;
}
if(empty($row['url'])){
    $row['url'] = "Not Set";
}else{
    $row['url'] = "<a href='".htmlspecialchars($row['url'])."'>".htmlspecialchars($row['url'])."</a>";
}?>
<div class="user-page">
<div id="user-content" class=" no-profile-post-user">
    <span class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], 0)?>" class="icon"></span>
	    <div class="nick-name"><?= htmlspecialchars($row['nickname'])?><span class="id-name"><?= htmlspecialchars($_GET['id']) ?></span></div>
    <div id="user-menu">
      <? if(isset($_SESSION['token']) && $row['id']==$user['id']){ ?><div id="edit-profile-settings"><a class="button symbol" href="/settings/profile">Profile Settings</a></div><? } ?>
    </div>
    <div class="user-action-content">
        <div class="toggle-button">
            <? if(isset($_SESSION['token']) && $user['id']!==$row['id']){ ?><button type="button" data-action="/users/<?= $_GET['id'] ?>.follow.json" class="follow-button button symbol <?= $is_follow ? 'none' : '' ?>">Follow</button><button type="button" data-action="/users/<?= $_GET['id'] ?>.unfollow.json" class="follow-button button symbol <?= $is_follow ? '' : 'none' ?>">Unfollow</button><? } ?>
        </div>
    </div>
  </div><div id="nav-menu" class="nav-4">
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/posts" class="<?= $_SERVER['REQUEST_URI']=='/users/'.$username.'/posts' || $_SERVER['REQUEST_URI']=='/users/'.$username.'/yeahs' ? 'selected' : '' ?> ? 'selected' : '' ?>">
      <span class="number"><?= $row['post_num'] ?></span>
      <span class="name">Posts</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/posts" class="<?= $_SERVER['REQUEST_URI']=='/users/'.$username.'/posts' || $_SERVER['REQUEST_URI']=='/users/'.$username.'/yeahs' ? 'selected' : '' ?> ? 'selected' : '' ?>">
      <span class="number">0 / 100</span>
      <span class="name">Friends</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/following" class="<?= $_SERVER['REQUEST_URI']=='/users/'.$username.'/following' ? 'selected' : '' ?>">
      <span class="number"><?= $row['followed_num'] ?></span>
      <span class="name">Following</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/followers" class="<?= $_SERVER['REQUEST_URI']=='/users/'.$username.'/followers' ? 'selected' : '' ?>">
      <span class="number"><?= $row['follow_num'] ?></span>
      <span class="name">Followers</span>
    </a>
  </div>
</div>
<?
if(!empty($row['description'])){?>
<div class="profile-comment">
<p><?= nl2br(htmlspecialchars($row['description']))?></p>
</div>
<?php } ?>
<div class="report-buttons-content">
    <button type="button" class="button" data-modal-open="#report-violation-page" data-action="/posts/<?=$row['id']?>/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="1">Report Violation&nbsp;&nbsp;&nbsp;</button>
</div>
<div id="report-violation-page" class="dialog none" data-modal-types="report report-violation" data-is-template="1">
<div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Report Violation to <?=SITE_NAME?> Administrators</h1>
                        <div class="window-body">
                            <p class="description">You are about to report a profile with content which violates the <?=SITE_NAME?> Code of Conduct. This report will be sent to the <?=SITE_NAME?> administrators and not to the creator of the profile.</p>
                            <form method="post" action="/posts/<?=$_GET["id"]?>/violations">
                                <input type="hidden" name="token" value="<?=$_SESSION["token"]?>">
                                <select name="type" class="can-report-spoiler" style="display: inline-block;">
                                    <option value="">Select who should see the report.</option>
                                    <option value="3" data-body-required="1"><?=SITE_NAME?> Administrators</option>
                                </select>
                                <textarea name="body" class="textarea" maxlength="500" data-placeholder="Enter a reason for the report here." placeholder="Enter a reason for the report here."></textarea>
                                <div class="form-buttons">
                                    <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
                                    <input type="submit" class="post-button black-button disabled" value="Submit Report" disabled="">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
</div>
<div class="user-data">
    <div class="user-main-profile data-content">
      <h4><span>Date created</span></h4>
      <div class="note"><?= date("m\/d\/y h:i:s A", strtotime($row['created_on'])) ?></div>
      <h4><span>User ID</span></h4>
      <div class="note">#<?= $row['id'] ?></div>
    </div>
  <div class="user-main-profile data-content">
      <h4><span>NNID</span></h4>
      <div class="note"><?= htmlspecialchars($row['nnid']) ?></div>
  </div>
  <div class="user-main-profile data-content">
      <h4><span>Custom URL</span></h4>
      <div class="note"><?= $row['url'] ?></div>
  </div>
</div>