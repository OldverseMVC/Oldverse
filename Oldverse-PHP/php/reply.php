<?php
require_once "lib/connect.php";
$stmt = $db->prepare("SELECT replies.id, replies.created_by, replies.body, replies.spoiler, replies.feeling, replies.created_at, replies.target, mii_hash, nickname, username, icon, level, name, community, (SELECT COUNT(*) FROM empathies WHERE target = replies.id AND type = 1) AS empathy_count, (SELECT UNIX_TIMESTAMP(replies.created_at)) AS timestamp, (SELECT nickname FROM users WHERE id = posts.created_by) AS ogpost_nickname, (SELECT mii_hash FROM users WHERE id = posts.created_by) AS ogpost_mii_hash FROM replies LEFT JOIN users ON created_by = users.id LEFT JOIN posts ON target = posts.id LEFT JOIN communities ON posts.community = communities.id WHERE replies.id = ?");
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
$title = $row['nickname']."'s reply";
$yeahs = getAllYeahs($_GET['id'], true);
require_once "lib/header.php";
$user = isset($user) ? $user : null;
?>
<div id="post-content" class="post reply-permalink-post">
<a class="post-permalink-button info-ticker arrow-left-button" href="/posts/<?= $row['target'] ?>">
    <span><span class="post-user-description">View <img src="<?= getAvatar($row['ogpost_mii_hash'], 0)?>" class="user-icon"><?= $row['ogpost_nickname'] ?>'s post for this comment.</span></span>
  </a>
<div id="reply-<?= $row['id']?>" class="">
<a href="/users/RixyRixy" class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], $row['feeling'])?>" class="icon"></a>
    <p class="timestamp-container"><span class="timestamp" href="/posts/<?= $_GET['id'] ?>"><?= getTimeAgo($row['timestamp']) ?> <?= $row['spoiler']==1 ? '- Spoilers!' : '' ?></span></p>
    <p class="user-name"><a href="/users/<?= htmlspecialchars($row['username']) ?>?>"><?= htmlspecialchars($row['nickname']) ?></a></p>
     <p class="community-container"> <? if(!empty($row['community'])){ ?><a href="/communities/<?= $row['community'] ?>"><img src="<?= $row['icon'] ?>" class="community-icon"><?= htmlspecialchars($row['name']) ?></a><? }else{ ?><a href="/activity"><img src="" class="community-icon">Activity Feed</a><? } ?></p><div class="body">
    <p class="reply-content-text"><?= nl2br(htmlspecialchars($row['body']))?></p>

    <div class="post-meta">
        <button type="button" class="symbol submit empathy-button <?= checkIfYeah($user['id'], $row['id'], true) ?> <?= !isset($_SESSION['token']) || $row['created_by']==$user['id'] ? 'disabled" disabled' : '"' ?> data-feeling="<?= getFeeling($row['feeling']) ?>" data-action="/replies/<?= $row['id'] ?>/empathies" ><span class="empathy-button-text"><?= getYeahText($row['feeling'], checkIfYeah($user['id'], $row['id'], true)) ?></span></button>
      <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?= $row['empathy_count'] ?></span></div>
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
<a href="/users/<?= $row_ya['username'] ?>" class="post-permalink-feeling-icon <?= $row_ya['level'] > 0 ? 'official-user' : '' ?>"><img src="<?= getAvatar($row_ya['mii_hash'], $row['feeling'])?>" class="user-icon"></a><? } $stmt->close();?></div><? }?>
    <div class="post-meta">
	
</div>
<div class="report-buttons-content">
    <button type="button" class="button" data-modal-open="#report-violation-page" data-action="/posts/<?=$row['id']?>/violations" data-is-post="1" data-is-permalink="1" data-can-report-spoiler="1">Report Violation</button>
</div>
<div id="report-violation-page" class="dialog none" data-modal-types="report report-violation" data-is-template="1">
<div class="dialog-inner">
                    <div class="window">
                        <h1 class="window-title">Report Violation to <?=SITE_NAME?> Administrators</h1>
                        <div class="window-body">
                            <p class="description">You are about to report a reply with content which violates the <?=SITE_NAME?> Code of Conduct. This report will be sent to the <?=SITE_NAME?> administrators and not to the creator of the reply or post.</p>
                            <form method="post" action="/posts/<?=$_GET["id"]?>/violations">
                                <input type="hidden" name="token" value="<?=$_SESSION["token"]?>">
                                <select name="type" class="can-report-spoiler" style="display: inline-block;">
                                    <option value="">Select who should see the report.</option>
                                    <option value="2" data-body-required="1"><?=SITE_NAME?> Administrators</option>
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