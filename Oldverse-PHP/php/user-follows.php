<?php
require_once "lib/connect.php";
if(!isset($_GET['id'])){
    showError(400, 'You must precise an user ID.');
}
if(!isset($_GET['mode'])){
    showError(400, 'You must precise an mode.');
}
$stmt = $db->prepare("SELECT id, nickname, mii_hash, description, created_on, level, favorite, (SELECT COUNT(*) FROM posts WHERE created_by = users.id) AS post_num, (SELECT COUNT(*) FROM follows WHERE target = users.id) AS follow_num, (SELECT COUNT(*) FROM follows WHERE source = users.id) AS followed_num FROM users WHERE username = ?");
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
if(isset($row['favorite'])){
    $stmt = $db->prepare("SELECT screenshot FROM posts WHERE id = ?");
    $stmt->bind_param('i', $row['favorite']);
    $stmt->execute();
    if($stmt->error){
        showError(500, 'An error occured.');
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        $row['favorite']=null;
    }
    $prow = $result->fetch_array();
}
$name = $_GET['mode'] == 1 ? 'follows' : 'followers';
$title = $row['nickname']."'s ".$name;
require_once "lib/header.php";
if($_GET['mode']==1){
    $stmt = $db->prepare("SELECT users.id, username, nickname, mii_hash, description, favorite, level FROM follows LEFT JOIN users ON target = users.id WHERE source = ? ORDER BY users.id DESC");
}else{
    $stmt = $db->prepare("SELECT users.id, username, nickname, mii_hash, description, favorite, level FROM follows LEFT JOIN users ON source = users.id WHERE target = ? ORDER BY users.id DESC");
}
$stmt->bind_param('i', $row['id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="user-page">
<? if(!empty($row['favorite'])){ ?><a href="/posts/<?= $row['favorite'] ?>" class="user-profile-memo-container" style="background-image:url('<?= htmlspecialchars($prow['screenshot'] )?>')"><img src="<?= htmlspecialchars($prow['screenshot']) ?>" class="user-profile-screenshot"></a><? } ?>
<div id="user-content" class="<?= !empty($row['favorite']) ? '' : 'no-profile-post-user' ?>">
    <span class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], 0)?>" class="icon"></span>
	    <div class="nick-name"><?= htmlspecialchars($row['nickname'])?><span class="id-name"><?= htmlspecialchars($_GET['id']) ?></span></div>
    <a href="/users/<?= $_GET['id'] ?>" class="profile-page-button button">To Top</a>
  </div><div id="nav-menu" class="nav-4">
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/posts">
      <span class="number"><?= $row['post_num'] ?></span>
      <span class="name">Posts</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/posts">
      <span class="number">0 / 100</span>
      <span class="name">Friends</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/following" class="<?= $_GET['mode']==1 ? 'selected' : '' ?>">
      <span class="number"><?= $row['followed_num'] ?></span>
      <span class="name">Following</span>
    </a>
    <a href="/users/<?= htmlspecialchars($_GET['id']) ?>/followers" class="<?= $_GET['mode']==2 ? 'selected' : '' ?>">
      <span class="number"><?= $row['follow_num'] ?></span>
      <span class="name">Followers</span>
    </a>
  </div>
</div>
<div class="list follow-list following">
<div id="user-page-no-content" class="none"></div>
  <ul class="list-content-with-icon-and-text arrow-list" id="following-list-content" data-next-page-url="">
    <?
    if($result->num_rows==0){
        showNoContent("No user was found.");
        exit;
    }
    while($row = $result->fetch_array()){
        if(isset($row['favorite'])){
            $stmt = $db->prepare("SELECT screenshot FROM posts WHERE id = ?");
            $stmt->bind_param('i', $row['favorite']);
            $stmt->execute();
            if($stmt->error){
                showError(500, 'An error occured.');
            }
            $fresult = $stmt->get_result();
            if($fresult->num_rows==0){
                $row['favorite']=null;
            }
            $prow = $fresult->fetch_array();
        }
    ?>
    <li class="trigger" data-href="/users/<?= htmlspecialchars($row['username']) ?>">
        <a href="/users/<?= htmlspecialchars($row['username']) ?>" class="icon-container <?= $row['level']>0 ? 'official-user' : ''  ?>"><img src="<?= getAvatar($row['mii_hash'], 0) ?>" class="icon"></a>
        <div class="body">
            <p class="title">
                <span class="nick-name"><a href="/users/<?= $row['username'] ?>"><?= htmlspecialchars($row['nickname']) ?></a></span>
                <span class="id-name"><?= htmlspecialchars($row['username']) ?></span>
            </p>
            <p class="text"><?= htmlspecialchars($row['description']) ?></p>
            <? if(isset($row['favorite'])){ ?><div class="user-profile-memo-content"><img src="<?= htmlspecialchars($prow['screenshot']) ?>" class="user-profile-memo"></div><? } ?>
        </div>
    </li>
    <? } ?>
    </ul>
</div>
