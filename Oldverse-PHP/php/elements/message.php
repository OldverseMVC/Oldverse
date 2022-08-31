<div id="post-<?= $row['id'] ?>" class="post trigger" tabindex="0">
  <span class="icon-container <?= $row['level'] > 0 ? 'official-user' : ''?>"><img src="<?= getAvatar($row['mii_hash'], $row['feeling']) ?>" class="icon"></span>
  <p class="timestamp-container">
    <a class="timestamp" href="/posts/<?= $row['id'] ?>"><?= getTimeAgo($row['timestamp']) ?></a>
  </p>
  <p class="user-name" style="color: <?= htmlspecialchars($row['nick_color']) ?>"><?= htmlspecialchars($row['nickname']) ?></p>
  <div class="body">
    <div class="post-content">


      <p class="post-content-text"><?= getBody($row['body'], false) ?></p>
        <div class="post-meta">
        </div>
    </div>
</div>
</div>