<?php
require_once "lib/connect.php";
if(isset($_SESSION['token'])){
    $user = getUser($_SESSION['token']);
    $userid = $user['id'];
}else{
    $userid = null;
}
$stmt = $db->prepare("SELECT name, icon, banner, communities.description, permissions, type, is_flipnote, nickname, username, owner, badge, (SELECT COUNT(*) FROM favorites WHERE source = ? AND target = communities.id) AS is_favorite FROM communities LEFT JOIN users ON owner = users.id WHERE communities.id = ?");
$stmt->bind_param('ii', $userid, $_GET['id']);
$stmt->execute();
if($stmt->error){
    showError(500, "An error occured while fetching the community from the database.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showError(404, "No community could be found with this ID.");
}
if(!isset($_GET['id'])){
    showError(403, "You need to specify a community ID.");
}
$row = $result->fetch_array();
$title = $row['name'];
require_once "lib/header.php";
if(empty($_GET['offset'])){
    $_GET['offset'] = 0;
}
$stmt = $db->prepare("SELECT posts.id, community, created_by, flipnote, body, posts.url, screenshot, spoiler, feeling, posts.created_at, mii_hash, nickname, nick_color, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE community = ? AND is_pinned = 0 ORDER BY posts.id DESC LIMIT 20 OFFSET ?");
$stmt->bind_param('ii', $_GET['id'], $_GET['offset']);
$stmt->execute();
$result = $stmt->get_result();
$stmt = $db->prepare("SELECT posts.id, community, created_by, flipnote, body, posts.url, screenshot, spoiler, is_pinned, feeling, posts.created_at, mii_hash, nickname, nick_color, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE community = ? AND is_pinned = 1 ORDER BY posts.id DESC");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$resultpinned = $stmt->get_result();
?>
<div id="page-title"><?= htmlspecialchars($row['name']) ?></div><div class="header-banner-container"><img src="<?= htmlspecialchars($row['banner']) ?>"></div>
<div id="community-content" class="">
  <span class="icon-container"><img src="<?= htmlspecialchars($row['icon']) ?>" class="icon"/></span>
  <?= getCommunityType($row['type'], false) ?>
  <? if(!empty($row['badge'])){ ?><span class="news-community-badge"><?= htmlspecialchars($row['badge']) ?></span><? } ?>
  <span class="title"><?= htmlspecialchars($row['name']) ?></span>
  <span class="text"><?= htmlspecialchars($row['description']) ?></span>
  <p style="text-align: center;">Community owner: <a href="/users/<?= htmlspecialchars($row['username'])?>"><?= htmlspecialchars($row['nickname']) ?></a></p>
  <div class="buttons-content">
      <?
      if(isset($_SESSION['token'])){?>
      <button type="button" class="symbol button favorite-button <?= $row['is_favorite'] > 0 ? 'checked' : '' ?>" data-action-favorite="/communities/<?= $_GET['id'] ?>/favorite.json" data-action-unfavorite="/communities/<?= $_GET['id'] ?>/unfavorite.json"><span class="favorite-button-text">Favorite</span></button>
      <? } ?>
  </div>
  <?php if(isset($_SESSION['username']) && $user['level'] > $row['permissions'] && $row['is_flipnote']!==1 || isset($_SESSION['username']) && $user['level'] == $row['permissions'] && $row['is_flipnote']!==1){ ?>
 <form id="post-form" method="post" action="/posts" class="folded for-identified-users">
  
  
  <input type="hidden" name="community" value="<?= $_GET['id'] ?>">
  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>
  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="Share your thoughts in a post to this community." data-open-folded-form="" data-required=""></textarea>
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
<?php } ?>
</div>
<div class="body-content" id="community-post-list" data-region="">
        <h3 class="label">Pinned posts</h3>
        <? if($resultpinned->num_rows!==0){ ?><div class="list">
        <? while($row = $resultpinned->fetch_array()){
            require "elements/post.php";
        }
        ?></div><? }else{?><p style="margin-top: 4px; color: #969696; font-size: 16px; text-align: center;">No pinned post was found on this community.</p><? }
        if($result->num_rows==0){
            showNoContent("No posts were found on this community.");
        }else{
            theelse:
            $new_offset = $_GET['offset'] + 20;?>
            <h3 class="label">Posts</h3>
            <div class="list post-list" data-next-page-url="/communities/<?= $_GET['id'] ?>?offset=<?= $new_offset ?>"><?
            while($row = $result->fetch_array()){
                require "elements/post.php";
            }
            ?></div><?
        }
        ?>
</div>