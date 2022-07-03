<?php
$title = "Activity Feed";
require_once "lib/header.php";
requireAuth();
?>
<div class="headline"><h2 class="headline-text">Activity Feed</h2><form method="GET" action="/users" class="search">
      <input type="text" name="query" placeholder="Search Users" minlength="2"/><input type="submit" value="q" title="Search"/>
    </form></div>
<form id="post-form" method="post" action="/activity/post" class="folded for-identified-users">
  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>
  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="Share your thoughts in a post to your followers." data-open-folded-form="" data-required=""></textarea>
 <input type="text" class="textarea-line url-form" name="url" placeholder="URL" maxlength="255">
  <input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">
 <br>
 <br>
    <label class="spoiler-button symbol">
    <input type="checkbox" id="is_spoiler" name="is_spoiler" value="1">
    Spoilers
  </label>
  
  
  <div class="form-buttons">
    <input type="submit" class="black-button post-button" value="Send">
  </div>
</form>
<?
if(empty($_GET['fragment']) && empty($_GET['offset'])){ ?>
    <div class="activity-feed content-loading-window">
      <div>
        <p class="tleft"><span>Loading activity feed...</span></p>
      </div>
    </div>
    <div class="activity-feed content-load-error-window none">
      <div>
        <p>The activity feed couldn't be loaded. Check your Internet connection, wait a moment, and then try reloading.</p>
        <div class="buttons-content"><a href="/activity" class="button">Reload</a></div>
      </div>
    </div><? } ?>
<?
//wanna hear a joke? t h i s 
$stmt = $db->prepare("SELECT target FROM `follows` WHERE source = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$users = "created_by = ".$user['id'];
while($row = $result->fetch_array()){
    $users = $users. " OR created_by = ".$row['target'];
}
$stmt = $db->prepare("SELECT target FROM `favorites` WHERE source = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_array()){
    $users = $users. " OR community = ".$row['target'];
}
if(empty($_GET['offset'])){
    $_GET['offset'] = 0;
}
$stmt = $db->prepare("SELECT posts.id, community, created_by, flipnote, body, posts.url, screenshot, spoiler, feeling, posts.created_at, mii_hash, nickname, nick_color, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE ".$users." ORDER BY posts.id DESC LIMIT 20 OFFSET ?");
$stmt->bind_param('i', $_GET['offset']);
$stmt->execute();
$result = $stmt->get_result();?>
<?php if(!empty($_GET['fragment']) || !empty($_GET['offset'])){ if($result->num_rows>0){ ?><div class="body-content" id="community-post-list" data-region="">
        <?php
        $new_offset = $_GET['offset'] + 20;?>
        <div class="list post-list" data-next-page-url="/activity?offset=<?= $new_offset ?>"><?
        while($row = $result->fetch_array()){
            require "elements/post.php";
        }
        ?></div>
</div><? }else{ showNoContent("No posts were found. Go follow some users/do some actions and get back to this page!"); }} ?>
