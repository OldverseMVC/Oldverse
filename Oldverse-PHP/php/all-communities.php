<?php
$title = "Communities";
require_once "lib/header.php";
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
$stmt = $db->prepare("SELECT id, icon, name, type FROM communities WHERE type = 0 OR type = 2 ORDER BY id DESC LIMIT 20 OFFSET ?");
$stmt->bind_param('i', $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt = $db->prepare("SELECT id, icon, name, type FROM communities WHERE type = 1 OR type = 2 ORDER BY id DESC LIMIT 20 OFFSET ?");
$stmt->bind_param('i', $offset);
$stmt->execute();
$result2 = $stmt->get_result();
$new_offset = $offset+20;
?>
<div class="headline">
    <h2 class="headline-text">All Communities</h2>
    <form method="GET" action="/communities/search" class="search">
      <input type="text" name="query" placeholder="Search Communities" minlength="2"/><input type="submit" value="q" title="Search"/>
    </form>
  </div>
  <div class="platform-tab">
    <a id="tab-wiiu" data-platform="wiiu" class="trigger selected" tabindex="0"><span>Wii U Communities</span></a>
    <a id="tab-3ds" data-platform="3ds" class="trigger" tabindex="0"><span>3DS Communities</span></a>
  </div>
  <div id="tab-wiiu-body" class="tab-body">
  <?php 
  if($result->num_rows>0){ ?>
    <ul class="list community-list community-title-list" data-next-page-url="/communities/all?offset=<?= $new_offset ?>">
        <?php
        while($row = $result->fetch_array()){ ?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php }  ?>
    </ul>
    <?php  }else{ showNoContent("No community was found."); }?>
  </div>
  <div id="tab-3ds-body" class="tab-body none">
  <?php 
  if($result->num_rows>0){ ?>
    <ul class="list community-list community-title-list" data-next-page-url="/communities/all?offset=<?= $new_offset ?>">
        <?php
        while($row = $result2->fetch_array()){ ?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php }  ?>
    </ul>
    <?php  }else{ showNoContent("No community was found."); }?>
  </div>
