<?php
require_once "lib/connect.php";
if(!isset($_SESSION['username']) && $_SERVER['REQUEST_URI']!=="/communities"){
    require_once "welcome.php";
    exit;
}
$title = "Communities";
require_once "lib/header.php";
$stmtposts = $db->query("SELECT posts.id, community, created_by, flipnote, body, posts.url, screenshot, spoiler, feeling, posts.created_at, mii_hash, nickname, nick_color, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE posts.created_at  >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY posts.id DESC LIMIT 20");
$stmtwiiu = $db->query("SELECT id, icon FROM communities WHERE featured = 1 AND type = 0 OR featured = 1 AND type = 2 ORDER BY id DESC LIMIT 10");
$stmtnewiiu = $db->query("SELECT id, icon, name, type FROM communities WHERE type = 0 OR type = 2 ORDER BY id DESC LIMIT 10");

$stmt3ds = $db->query("SELECT id, icon FROM communities WHERE featured = 1 AND type = 1 OR featured = 1 AND type = 2 ORDER BY id DESC LIMIT 10");
$stmtnew3ds = $db->query("SELECT id, icon, name, type FROM communities WHERE type = 1 OR type = 2 ORDER BY id DESC LIMIT 10");
if(isset($_SESSION['username'])){
    $stmtfav = $db->prepare("SELECT communities.id, icon, name, type FROM favorites LEFT JOIN communities ON target = communities.id WHERE source = ? ORDER BY favorites.id DESC");
    $stmtfav->bind_param('i', $user['id']);
    $stmtfav->execute();
    $resultfav = $stmtfav->get_result();
}
if(!isset($_SESSION['username'])){
?>
<h2 class="welcome-message">Welcome to <?=SITE_NAME?>!</h2>
<div id="about">
    <img src="/assets/img/welcome-image.png">
    <p><?= MEMO ?></p>
</div>
<?php }?>
<?php if($stmtposts->num_rows!==0){ ?>
<h2 class="headline">Yo momma (Latest posts)</h2>
<div class="list post-list">
<?php 
$i = 0;
while($row = $stmtposts->fetch_array()){
    if($i!==0){
        $display_none = true;
    }
    require "elements/post.php";
    $i++;
}
?>
</div>
<?php } ?>
<div class="body-content" id="community-top" data-region="USA">
<div class="headline">
    <h2 class="headline-text">Communities</h2>
    <form method="GET" action="/communities/search" class="search">
      <input type="text" name="query" placeholder="Search Communities" minlength="2"/><input type="submit" value="q" title="Search"/>
    </form>
  </div>
  <p class="center" style="padding: 8px;"><b><?= SITE_NAME ?> stores cookies on your computer. <a href="/cookie-info">See more here.</a></b></p>
  <?php if(isset($_SESSION['username']) && $resultfav->num_rows > 0) { ?>
  <h3 class="label">Favorite Communities</h3>
    <ul class="list community-list community-title-list">
        <?php
        while($row = $resultfav->fetch_array()){ ?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>
  <div id="identified-user-banner">
    <a href="/identified_user_posts" data-pjax="#body" class="list-button us">
      <span class="title">Get the latest news here!</span>
      <span class="text">Posts from Verified Users</span>
    </a>
  </div>
  <a href="/communities/create" class="button symbol create-button">Create community</a>
  <div class="platform-tab">
    <a id="tab-wiiu" data-platform="wiiu" class="trigger selected" tabindex="0"><span>Wii U Communities</span></a>
    <a id="tab-3ds" data-platform="3ds" class="trigger" tabindex="0"><span>3DS Communities</span></a>
  </div>
  <div id="tab-wiiu-body" class="tab-body">
    <?php
    if($stmtwiiu->num_rows!==0){ ?>
    <h3 class="label label-wiiu"><img src="/assets/img/hot-icon-wiiu.png" class="hot-icon">Spotlight</h3>
    <ul class="icon-list">
        <?php
        while($row = $stmtwiiu->fetch_array()){ ?>
      <li>
        <a href="/communities/<?= $row['id'] ?>" data-pjax="#body">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
        </a>
        
      </li>
      <?php } ?>
      </ul>
      <?php } ?>
        <h3 class="label label-wiiu">New Communities</h3>
            <ul class="list community-list community-title-list">
        <?php
        if($stmtnewiiu->num_rows==0){
            showNoContent("No community of this type was found. Be the first to create one!");
        }
        while($row = $stmtnewiiu->fetch_array()){ ?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php } ?>
        </ul>
    </div>
      <div id="tab-3ds-body" class="tab-body none">
    <?php
    if($stmt3ds->num_rows!==0){ ?>
    <h3 class="label label-3ds"><img src="/assets/img/hot-icon-3ds.png" class="hot-icon">Spotlight</h3>
    <ul class="icon-list">
      <?php
        while($row = $stmt3ds->fetch_array()){ ?>
      <li>
        <a href="/communities/<?= $row['id'] ?>" data-pjax="#body">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
        </a>
        
      </li>
      <?php } ?>
     </ul>
     <?php } ?>
     <h3 class="label label-3ds">New Communities</h3>
            <ul class="list community-list community-title-list">
        <?php
        if($stmtnew3ds->num_rows==0){
            showNoContent("No community of this type was found. Be the first to create one!");
        }
        while($row = $stmtnew3ds->fetch_array()){ ?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php } ?>
        </ul>
    </div>
    <a href="/communities/all" class="button">Show All</a>
</div>
<div id="about">
  <a href="/qna" class="arrow-button"><span>Q&amp;A</span></a>
  <a href="/codeofconduct" class="arrow-button"><span><?= SITE_NAME ?> Code of Conduct</span></a>
  <a href="https://github.com/Rix565/Oldverse" class="arrow-button"><span>Oldverse on GitHub</span></a>
</div>
<ul id="footer-selector">
    <li><strong>Made with <3 by Oldverse Team</strong></li>
    <li>This website is <strong>not-for-profit</strong>, please support Nintendo instead.</li>
</ul>
