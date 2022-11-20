<?php
$title = "Notifications";
require_once "lib/header.php";
requireAuth();
$stmt = $db->prepare("SELECT news.id, type, news.url, username, nickname, mii_hash, level, is_read, additional_id, (SELECT UNIX_TIMESTAMP(news.timestamp)) AS tmstp FROM news LEFT JOIN users ON source = users.id WHERE target = ? ORDER BY news.id DESC LIMIT 20");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showJSONError(500, 548488, "An error occured while checking notifications.");
}
$result = $stmt->get_result();
?>
<h1 class="headline">Notifications</h1>
<ul class="list news-list" data-next-page-url="">
    <?php 
    if($result->num_rows==0){
        showNoContent("No updates.");
    }
    while($row = $result->fetch_array()){
        switch($row['type']){
            case 1:
                $stmt = $db->prepare("SELECT body FROM posts WHERE id = ?");
                $stmt->bind_param('i', $row['additional_id']);
                $stmt->execute();
                $bresult = $stmt->get_result();
                $brow = $bresult->fetch_array();
                $drycut = substr($brow['body'], 0, 50);
                $cut = $drycut == $brow['body'] ? getBody(htmlspecialchars($brow['body'])) : getBody(htmlspecialchars($drycut))."...";                
                $builded_type = " yeahed your post (".$cut.")";
                break;
            case 2:
                $stmt = $db->prepare("SELECT body FROM posts WHERE id = ?");
                $stmt->bind_param('i', $row['additional_id']);
                $stmt->execute();
                $bresult = $stmt->get_result();
                $brow = $bresult->fetch_array();
                $drycut = substr($brow['body'], 0, 50);
                $cut = $drycut == $brow['body'] ? getBody(htmlspecialchars($brow['body'])) : getBody(htmlspecialchars($drycut))."...";  
                $builded_type = " commented on your post (".$cut.")";
                break;
            case 3:
                $stmt = $db->prepare("SELECT body FROM replies WHERE id = ?");
                $stmt->bind_param('i', $row['additional_id']);
                $stmt->execute();
                $bresult = $stmt->get_result();
                $brow = $bresult->fetch_array();
                $drycut = substr($brow['body'], 0, 50);
                $cut = $drycut == $brow['body'] ? getBody(htmlspecialchars($brow['body'])) : getBody(htmlspecialchars($drycut))."...";  
                $builded_type = " yeahed your reply (".$cut.")";
                break;
            case 4:
                $builded_type = " followed you";
                break;
            case 5:
                $stmt = $db->prepare("SELECT body FROM posts WHERE id = ?");
                $stmt->bind_param('i', $row['additional_id']);
                $stmt->execute();
                $bresult = $stmt->get_result();
                $brow = $bresult->fetch_array();
                $drycut = substr($brow['body'], 0, 50);
                $cut = $drycut == $brow['body'] ? getBody(htmlspecialchars($brow['body'])) : getBody(htmlspecialchars($drycut))."...";                
                $builded_type = " mentionned you on their post (".$cut.")";
                break;
            case 6:
                $stmt = $db->prepare("SELECT body FROM replies WHERE id = ?");
                $stmt->bind_param('i', $row['additional_id']);
                $stmt->execute();
                $bresult = $stmt->get_result();
                $brow = $bresult->fetch_array();
                $drycut = substr($brow['body'], 0, 50);
                $cut = $drycut == $brow['body'] ? getBody(htmlspecialchars($brow['body'])) : getBody(htmlspecialchars($drycut))."...";  
                $builded_type = " mentionned you on their reply (".$cut.")";
                break;
        }
        if($row['is_read']==0){
            $stmt = $db->prepare("UPDATE news SET is_read = 1 WHERE id = ?");
            $stmt->bind_param('i', $row['id']);
            $stmt->execute();
        }?>
        <li class="trigger <?= $row['is_read'] == 0 ? 'notify' : ''?>" data-href="<?= $row['url'] ?>">
            <a href="/users/<?= htmlspecialchars($row['username']) ?>" class="icon-container <?= $row['level']>0 ? 'official-user' : ''  ?>"><img src="<?= getAvatar($row['mii_hash'], 0) ?>" class="icon"></a>
            <div class="body">
                <p class="title">
                    <p><a class="nick-name" href="/users/<?= htmlspecialchars($row['username']) ?>"><?= htmlspecialchars($row['nickname']) ?></a><?= $builded_type ?>.</p>
                </p>
                <span class="timestamp"><?= getTimeAgo($row['tmstp']) ?></span>
            </div>
        </li>
    <?php  } ?>
</ul>