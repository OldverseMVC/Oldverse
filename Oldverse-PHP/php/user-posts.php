<?php
require_once "lib/connect.php";
if(!isset($_GET['id'])){
    showError(400, 'You must precise an user ID.');
}
if(!isset($_GET['mode'])){
    showError(400, 'You must precise an mode.');
}
$stmt = $db->prepare("SELECT id, nickname, mii_hash, level, (SELECT COUNT(*) FROM posts WHERE created_by = users.id) AS post_num, (SELECT COUNT(*) FROM empathies WHERE source = users.id) AS yeah_num, (SELECT COUNT(*) FROM follows WHERE target = users.id) AS follow_num, (SELECT COUNT(*) FROM follows WHERE source = users.id) AS followed_num FROM users WHERE username = ?");
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
$name = $_GET['mode']==1 ? 'posts' : 'yeahs';
$name_2 = $_GET['mode']==1 ? 'posts' : 'empathies';
$title = $row['nickname']."'s ".$name;
require_once "lib/header.php";
if(empty($_GET['offset'])){
    $_GET['offset'] = 0;
}
if($_GET['mode']==1){
    $stmt = $db->prepare("SELECT posts.id, community, created_by, body, posts.url, screenshot, spoiler, feeling, posts.created_at, mii_hash, nickname, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE created_by = ? ORDER BY posts.id DESC LIMIT 20 OFFSET ?");
}else{
    $stmt = $db->prepare("SELECT posts.id, community, created_by, body, posts.url, screenshot, spoiler, feeling, posts.created_at, mii_hash, nickname, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM empathies LEFT JOIN posts ON target = posts.id LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE empathies.source = ? AND empathies.type = 0 ORDER BY empathies.id DESC LIMIT 20 OFFSET ?");
}
$stmt->bind_param('ii', $row['id'], $_GET['offset']);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="user-page">
<div id="user-content" class=" no-profile-post-user">
    <span class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], 0)?>" class="icon"></span>
	    <div class="nick-name"><?= htmlspecialchars($row['nickname'])?><span class="id-name"><?= htmlspecialchars($_GET['id']) ?></span></div>
    <a href="/users/<?= $_GET['id'] ?>" class="profile-page-button button">To Top</a>
  </div><div id="nav-menu" class="nav-4">
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/posts" class="selected">
      <span class="number"><?= $row['post_num'] ?></span>
      <span class="name">Posts</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/posts" class="">
      <span class="number">0 / 100</span>
      <span class="name">Friends</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/following">
      <span class="number"><?= $row['followed_num'] ?></span>
      <span class="name">Following</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/followers">
      <span class="number"><?= $row['follow_num'] ?></span>
      <span class="name">Followers</span>
    </a>
  </div>
  <div class="tab2 user-menu-activity">
  <a href="/users/<?= $_GET['id'] ?>/posts" class="<?= $_GET['mode']== 1 ? "selected" : ''?>"><span class="label">Posts</span><span class="number"><?= $row['post_num'] ?></span></a>
  <a href="/users/<?= $_GET['id'] ?>/empathies" class="<?= $_GET['mode']== 2 ? "selected" : ''?>"><span class="label">Yeahs</span><span class="number"><?= $row['yeah_num'] ?></span></a>
</div>
<?php if($result->num_rows>0){ ?><div class="body-content" id="community-post-list" data-region="">
        <?php
        if($result->num_rows==0){
            showNoContent("No posts were found.");
        }else{
            $new_offset = $_GET['offset'] + 20;
            echo '<div class="list post-list" data-next-page-url="/users/'.$_GET['id'].'/'.$name_2.'?offset='.$new_offset.'">';
            while($row = $result->fetch_array()){
                require "elements/post.php";
            }
            echo '</div>';
        }
        ?>
</div><? } ?>
</div>