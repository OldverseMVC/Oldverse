<?php
require_once "lib/connect.php";
if(!isset($_GET['id'])){
    showError(400, "You must give an post ID.");
}
$stmt = $db->prepare("SELECT posts.id, community, created_by, flipnote, body, posts.url, screenshot, spoiler, feeling, is_locked, is_pinned, created_at, mii_hash, nick_color, nickname, username, is_pinned, level, icon, name, permissions, owner, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE posts.id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showError(500, "An error occured while trying to fetch the post from the database.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showError(404, "The post could not be found.");
}
$row = $result->fetch_array();
$locked = $row['is_locked'];
$stmt = $db->prepare("SELECT replies.id, replies.created_by, replies.body, replies.screenshot, replies.feeling, mii_hash, nickname, nick_color, username, level, replies.created_at, replies.spoiler, (SELECT COUNT(*) FROM empathies WHERE target = replies.id AND type = 1) AS empathy_count, (SELECT UNIX_TIMESTAMP(replies.created_at)) AS timestamp, (SELECT id FROM users WHERE id = posts.created_by) AS target_id FROM replies LEFT JOIN users ON replies.created_by = users.id LEFT JOIN posts ON target = posts.id WHERE target = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showError(500, "An error occured while trying to fetch the comments from the database.");
}
$rresult = $stmt->get_result();
$title = $row['nickname']."'s post";
$yeahs = getAllYeahs($_GET['id']);
require_once "lib/header.php";
$user = isset($user) ? $user : null;
?>
<div id="page-title"><? if(!empty($row['community'])){ ?><?= htmlspecialchars($row['name']) ?><? }else{ ?>Activity Feed<? } ?></div>
<div id="post-content" class="post <?= $row['spoiler']==1 ? 'hidden' : '' ?>" <?= $row['spoiler']==1 ? 'data-href-hidden="/posts/'.$row['id'].'"' : '' ?>>
    <?if($user['id'] == $row['created_by'] || $row['owner'] == $user['id'] || $user['level'] > 0){ ?><button type="button" class="symbol button edit-button profile-post-button" data-modal-open="#edit-post-page" data-is-post="1"><span class="symbol-label">Edit post</span></button><? } ?>
  <a href="/users/<?= $row['username'] ?>" class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], $row['feeling']) ?>" class="icon"></a>
  <p class="timestamp-container">
    <a class="timestamp" href="/posts/<?= $_GET['id'] ?>"><?= getTimeAgo($row['timestamp']) ?> <?= $row['spoiler']==1 ? '- Spoilers!' : '' ?></a>
  </p>
  <p class="user-name" style="color: <?= htmlspecialchars($row['nick_color']) ?>"><?= htmlspecialchars($row['nickname']) ?><span class="user-id"><?= $row['username'] ?></span></p>
 <p class="community-container"> <? if(!empty($row['community'])){ ?><a href="/communities/<?= $row['community'] ?>"><img src="<?= $row['icon'] ?>" class="community-icon"><?= htmlspecialchars($row['name']) ?></a><? }else{ ?><a href="/activity"><img src="" class="community-icon">Activity Feed</a><? } ?></p>

  <div class="body">
    <div class="post-content">
        
        <? if($row['is_pinned']==1){ ?><p><b>Pinned post</b></p><? } ?>
      <p class="post-content-text"><?= getBody($row['body']) ?></p>
      <? if(!empty($row['screenshot'])){ ?><p class="screenshot-container still-image"><img src="<?= htmlspecialchars($row['screenshot'])?>"></p><? } ?>
      <?
      if($row['spoiler']==1){ ?>
      <div class="hidden-content">
            <p>This Post may contain spoilers, view at your own risk!<button type="button" class="hidden-content-button">View Post</button></p>
        </div>
    <? } ?>
    <? if(!empty($row['flipnote'])){?>
        <flipnote-player style="--flipnote-player-icon-color: green; --flipnote-player-button-background: #46b046;  --flipnote-player-slider-track: #46b046; --flipnote-player-slider-level: green; --flipnote-player-slider-handle: green;" src="/flipnote/kwz/<?= $row['flipnote'] ?>.kwz"></flipnote-player>
    <? } ?>
    <?
    if(preg_match('/(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\/?\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11})/', $row['url'], $matches)) {?>
        <div class="screenshot-container video"><iframe class="youtube-player" type="text/html" width="40" height="40" src="https://www.youtube.com/embed/<?= htmlspecialchars($matches[1]) ?>?rel=0&amp;modestbranding=1&amp;iv_load_policy=3" frameborder="0"></iframe></div>
    <? }else{
    ?>
    <p class="url-link"><a href="<?= htmlspecialchars($row['url'])?>" target="_blank"><?= htmlspecialchars($row['url']) ?></a></p>
    <? } ?>

      <div class="post-meta">
        <button type="button" class="post symbol submit empathy-button <?= checkIfYeah($user['id'], $row['id']) ?> <?= !isset($_SESSION['token']) || $row['created_by']==$user['id'] ? 'disabled" disabled' : '"' ?> data-feeling="<?= getFeeling($row['feeling']) ?>" data-action="/posts/<?= $row['id'] ?>/empathies" data-yeah-type="post"><span class="empathy-button-text"><?= getYeahText($row['feeling'], checkIfYeah($user['id'], $row['id'])) ?></span></button>
        <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?= $row['empathy_count'] ?></span></div>
        <div class="reply symbol"><span class="symbol-label">Comments</span><span class="reply-count"><?= $row['reply_count'] ?></span></div>
      </div>
    </div>
  </div>
 </div>
<? if($row['empathy_count']!==0){ ?>
<div id="empathy-content">
<? while($row_y = $yeahs->fetch_array()){
    $stmt = $db->prepare("SELECT username, mii_hash, level FROM users WHERE id = ?");
    $stmt->bind_param('i', $row_y['source']);
    $stmt->execute();
    if($stmt->error){
        showError(500, 'An error occured while fetching yeahs authors.');
    }
    $cool_result = $stmt->get_result();
    $row_ya = $cool_result->fetch_array();?>
<a href="/users/<?= $row_ya['username'] ?>" class="post-permalink-feeling-icon <?= $row_ya['level'] > 0 ? 'official-user' : '' ?>"><img src="<?= getAvatar($row_ya['mii_hash'], $row['feeling'])?>" class="user-icon"></a><? } $stmt->close(); ?></div><?}?>
<!-- NOTE TO OLDVERSE DEVS: DO NOT REMOVE THIS (!!!!!) else spacing will be broken-->
<div class="buttons-content">
    <div class="social-buttons-content">
        
    </div>
    <div class="report-buttons-content">
        <button type="button" class="button" data-modal-open="#report-violation-page" data-action="/posts/<?= $_GET['id'] ?>/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="1">Report Violation</button>
    </div>
</div>
<? if($user['id'] == $row['created_by'] || $row['owner'] == $user['id'] || $user['level'] > 0){ ?>
<div id="edit-post-page" class="dialog none" data-modal-types="edit-post">
<div class="dialog-inner">
  <div class="window">
    <h1 class="window-title">Edit Post</h1>
    <div class="window-body">
      <form method="post" class="edit-post-form">
        <p class="select-button-label">Select an option.</p>
        <select name="edit-type">
          <option value selected>Select an option.</option>
          <? if(!empty($row['screenshot'])){ ?><option value="screenshot-profile-post" data-action="/posts/<?= $_GET['id'] ?>/favorite">Set as favorite post</option><? } ?>
            <option value="spoiler" data-action="/posts/<?= $_GET['id'] ?>.set_spoiler.json">Set as spoiler</option>
          <option value="delete" data-action="/posts/<?= $_GET['id'] ?>.delete.json">Delete</option>
            <? if($row['owner'] == $user['id'] && $row['is_pinned']!==1 || $user['level'] > 0 && $row['is_pinned']!==1){ ?><option value="pin" data-action="/posts/<?= $_GET['id'] ?>.pin.json">Pin post</option><? } ?>
            <? if($row['owner'] == $user['id'] && $row['is_locked']!==1 || $user['level'] > 0 && $row['is_locked']!==1){ ?><option value="pin" data-action="/posts/<?= $_GET['id'] ?>.lock.json">Lock post</option><? } ?>
        </select>
         <div class="form-buttons">
          <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
          <input type="submit" class="post-button black-button" value="Confirm">
        </div>
    </form>
    </div>
</div>
</div></div>
  <? } ?>
<?php if($user['level'] < $row['permissions']){ ?>
<div id="report-violation-page" class="dialog none" data-modal-types="report report-violation" data-is-template="1">
<div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Report Violation to <?=SITE_NAME?> Administrators</h1>
                        <div class="window-body">
                            <p class="description">You cannot report posts in this community.</p>
                            <form>
                                <div class="form-buttons">
                                    <input type="button" class="olv-modal-close-button gray-button" value="OK">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
</div>
<?php } else {?>
<div id="report-violation-page" class="dialog none" data-modal-types="report report-violation" data-is-template="1">
<div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Report Violation to <?=SITE_NAME?> Administrators</h1>
                        <div class="window-body">
                            <p class="description">You are about to report a post with content which violates the <?=SITE_NAME?> Code of Conduct. This report will be sent to the <?=SITE_NAME?> administrators and not to the creator of the post.</p>
                            <form method="post" action="/posts/<?= $_GET['id'] ?>/violations">
                                <input type="hidden" name="token" value="<?=$_SESSION["token"]?>">
                                <select name="type" class="can-report-spoiler" style="display: inline-block;">
                                    <option value="">Select who should see the report.</option>
                                    <option value="1" data-body-required="1"><?=SITE_NAME?> Administrators</option>
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
<?php } ?>
<div id="reply-content">
  <h2 class="label">Comment</h2>
  <div class="no-reply-content <?= $row['reply_count'] > 0 ? 'none' : '' ?>"><div>
    <p>This post has no comments.</p>
  </div></div>
<ul class="list reply-list js-post-list">
    <?
    while($row = $rresult->fetch_array()){
        require 'elements/reply.php';
     } ?>
</ul>
<h2 class="label">Add a Comment</h2>
<?php
if(isset($_SESSION['token'])){ 
    if($locked!==1){ ?>
<form id="reply-form" method="post" class="folded" action="/posts/<?= $_GET['id'] ?>/replies">
  

  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>


  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="Add a comment to this post here." data-open-folded-form="" data-required=""></textarea>
     <input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">
     <br><br>
  <label class="spoiler-button symbol">
    <input type="checkbox" id="is_spoiler" name="is_spoiler" value="1">
    Spoilers
  </label>
  <div class="form-buttons">
    <input type="submit" class="black-button reply-button disabled" value="Send">
  </div>
</form>
<?php }else{?>
<div id="about">
<p>This post has been locked and is not open for further comments.</p><br>
</div><? } }else{ ?>
<div id="about">
<p>You must sign in to post a comment.<br><br>Sign in with a <?=SITE_NAME?> account to connect to users around the world by writing posts and comments and by giving Yeahs to other people's posts. You can sign up for a <?=SITE_NAME?> account <a href="/account/signup">here</a>.</p><br>
<a href="/account/login" class="arrow-button"><span>Login</span></a>
<a href="/account/signup" class="arrow-button"><span>Sign up</span></a>
<a href="/codeofconduct" class="arrow-button"><span><?=SITE_NAME?> Code Of Conduct</span></a>
</div>
<?php } ?>
