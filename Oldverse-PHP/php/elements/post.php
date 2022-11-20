<?php  $user = isset($user) ? $user : null;
$display_none = isset($display_none) ? $display_none : false; ?>
<div id="post-<?= $row['id'] ?>" data-href="/posts/<?= $row['id'] ?>" class="post trigger <?= $row['spoiler']==1 ? 'hidden' : '' ?>" <?= $row['spoiler']==1 ? 'data-href-hidden="/posts/'.$row['id'].'"' : '' ?> tabindex="0" <?= $display_none ? 'style="display: none;"' : ''?> >
  <span class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], $row['feeling']) ?>" class="icon"></span>
  <p class="timestamp-container">
    <a class="timestamp" href="/posts/<?= $row['id'] ?>"><?= getTimeAgo($row['timestamp']) ?> <?= $row['spoiler']==1 ? '- Spoilers!' : '' ?></a>
  </p>
  <p class="user-name" style="color: <?= htmlspecialchars($row['nick_color']) ?>"><?= htmlspecialchars($row['nickname']) ?></p>
  <?php  if(!empty($row['community'])){ ?><p class="community-container"><a href="/communities/<?= $row['community'] ?>"><img src="<?= $row['icon'] ?>" class="community-icon"><?= htmlspecialchars($row['name']) ?></a></p><?php  } ?>

  <div class="body">
    <div class="post-content">


      <p class="post-content-text"><?= getBody($row['body'], true, 200) ?></p>
      <?php  if(!empty($row['screenshot'])){ ?><p class="screenshot-container still-image"><img src="<?= htmlspecialchars($row['screenshot'])?>"></p><?php  } ?>
      <?php 
      if($row['spoiler']==1){ ?>
      <div class="hidden-content">
            <p>This Post may contain spoilers, view at your own risk!<button type="button" class="hidden-content-button">View Post</button></p>
        </div>
    <?php  } ?>
    <?php 
    if(!empty($row['flipnote'])){?>
        <div class="screenshot-container video"><img src="/flipnote/thumb/<?= $row['flipnote'] ?>.jpg"></div>
    <?php  } ?>
    <?php 
    if(preg_match('/(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\/?\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11})/', $row['url'], $matches)) {?>
        <div class="screenshot-container video"><img src="https://i.ytimg.com/vi/<?= htmlspecialchars($matches[1]) ?>/sddefault.jpg"></iframe></div>
    <?php  }else{
    ?>
    <p class="url-link"><a href="<?= htmlspecialchars($row['url'])?>" target="_blank"><?= htmlspecialchars($row['url']) ?></a></p>
    <?php  } ?>

      <div class="post-meta">
        <?php  if($_SERVER['REQUEST_URI']!=="/"){ ?><button type="button" class="symbol submit empathy-button <?= checkIfYeah($user['id'], $row['id']) ?> <?= !isset($_SESSION['token']) || $row['created_by']==$user['id'] ? 'disabled" disabled' : '"' ?> data-feeling="<?= getFeeling($row['feeling']) ?>" data-action="/posts/<?= $row['id'] ?>/empathies" ><span class="empathy-button-text"><?= getYeahText($row['feeling'], checkIfYeah($user['id'], $row['id'])) ?></span></button><?php  } ?>
        <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count"><?= $row['empathy_count'] ?></span></div>
        <div class="reply symbol"><span class="symbol-label">Comments</span><span class="reply-count"><?= $row['reply_count'] ?></span></div>
      </div>
    </div>
  </div>
</div>
