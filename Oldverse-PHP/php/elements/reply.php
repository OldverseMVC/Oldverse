<?php  $user = isset($user) ? $user : null; ?>
<li id="reply-<?= $row['id'] ?>" data-href="/replies/<?= $row['id'] ?>" class="reply <?= $row['target_id']==$row['created_by'] ? 'my' : 'other'?> trigger <?= $row['spoiler']==1 ? 'hidden' : '' ?>" <?= $row['spoiler']==1 ? 'data-href-hidden="/replies/'.$row['id'].'"' : '' ?> tabindex="0">
  <a href="/users/<?= $row['username'] ?>" class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], $row['feeling'])?>" class="icon"></a>
  <div class="body">
      <div class="header">
      <p class="user-name"><a href="/users/<?= $row['username'] ?>" style="color: <?= htmlspecialchars($row['nick_color']) ?>"><?= $row['nickname'] ?></a></p>
      <p class="timestamp-container">
        <a class="timestamp" href="/replies/<?= $_GET['id'] ?>"><?= getTimeAgo($row['timestamp']) ?> <?= $row['spoiler']==1 ? '- Spoilers!' : '' ?></a>
    </p>
    </div>
    
      <p class="reply-content-text"><?= getBody($row['body'], true, 500) ?></p>
    <?php  if(!empty($row['screenshot'])){ ?><p class="screenshot-container still-image"><img src="<?= htmlspecialchars($row['screenshot'])?>"></p><?php  } ?>
    
    <?php  if($row['spoiler']==1){ ?>
      <div class="hidden-content">
            <p>This reply may contain spoilers, view at your own risk!<button type="button" class="hidden-content-button">View Reply</button></p>
        </div>
    <?php  } ?>


    <div class="reply-meta">
        <button type="button" class="reply symbol submit empathy-button <?= checkIfYeah($user['id'], $row['id'], true) ?> <?= !isset($_SESSION['token']) || $row['created_by']==$user['id'] ? 'disabled" disabled' : '"' ?> data-feeling="<?= getFeeling($row['feeling']) ?>" data-action="/replies/<?= $row['id'] ?>/empathies" data-yeah-type="comment"><span class="empathy-button-text"><?= getYeahText($row['feeling'], checkIfYeah($user['id'], $row['id'], true)) ?></span></button>
        <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?= $row['empathy_count'] ?></span></div>
    </div>
	  </div>
</li>
