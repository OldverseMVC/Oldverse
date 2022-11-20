<?php
$title = "Messages list";
require_once "lib/header.php";
requireAuth();
$stmt = $db->prepare("SELECT conversations.id, cid, target, nickname, username, mii_hash, level FROM conversations LEFT JOIN users ON users.id=target WHERE source = ? ORDER BY conversations.id DESC");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<h1 class="headline">Messages list</h1>
<ul class="list news-list" data-next-page-url="">
    <?php
    if($result->num_rows==0){
        showNoContent("You don't have any messages yet.");
    }
    while($row = $result->fetch_array()){
        $stmt = $db->prepare("SELECT body, is_read, author, (SELECT UNIX_TIMESTAMP(created_at)) AS timestamp FROM dms WHERE cid = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('i', $row['cid']);
        $stmt->execute();
        $dresult = $stmt->get_result();
        $drow = $dresult->fetch_assoc();
        $drycut = substr($drow['body'], 0, 50);
        $cut = $drycut == $drow['body'] ? getBody(htmlspecialchars($drow['body'])) : getBody(htmlspecialchars($drycut))."...";  ?>
       <li class="trigger<?= $drow['is_read']==0 && $drow['author']!==$user['id'] ? " notify" : "" ?>" data-href="/messages/<?= htmlspecialchars($row['username']) ?>">
            <a href="/users/<?= htmlspecialchars($row['username']) ?>" class="icon-container <?= $row['level']>0 ? 'official-user' : ''  ?>"><img src="<?= getAvatar($row['mii_hash'], 0) ?>" class="icon"></a>
            <div class="body">
                <p class="title">
                    <p><a class="nick-name" href="/users/<?= htmlspecialchars($row['username']) ?>"><?= htmlspecialchars($row['nickname']) ?></a><br><?= $dresult->num_rows==0 ? "You haven't exchanged messages with this user yet." : $cut ?></p>
                </p>
                <?php  if($dresult->num_rows!==0){ ?><span class="timestamp"><?= getTimeAgo($drow['timestamp']) ?></span><?php  } ?>
            </div>
        </li> 
        <?php 
    }
    ?>
</ul>