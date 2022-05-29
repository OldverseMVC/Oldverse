<?php
//needs a rewrite i think idk yet
$title = "Search";
require_once "lib/header.php";
if(!isset($_GET["query"])){
    header("Location: /");
}
// TODO: make the search more efficient
// Rixy: Working on that RN
// 05/29/22 update: bit better but not what we want yet
$stmt = $db->prepare("SELECT id, name, icon, type FROM `communities` WHERE name LIKE CONCAT('%', ?, '%') AND type = 0 OR name LIKE CONCAT('%', ?, '%') AND type = 2");
$stmt->bind_param("ss", $_GET["query"], $_GET['query']);
$stmt->execute();
$resultwiiu = $stmt->get_result();
$stmt = $db->prepare("SELECT id, name, icon, type FROM `communities` WHERE name LIKE CONCAT('%', ?, '%') AND type = 1 OR name LIKE CONCAT('%', ?, '%') AND type = 2");
$stmt->bind_param("ss", $_GET["query"], $_GET['query']);
$stmt->execute();
$result3ds = $stmt->get_result();
?>
<div class="body-content" id="search-top" data-region="USA">
    <div class="headline">
    <h2 class="headline-text">Search - "<?=htmlspecialchars($_GET["query"])?>"</h2>
    <form method="GET" action="/communities/search" class="search">
      <input type="text" name="query" placeholder="Search Communities" minlength="2" value="<?= htmlspecialchars($_GET['query']) ?>"/><input type="submit" value="q" title="Search"/>
    </form>
    </div>
  <div class="platform-tab">
    <a id="tab-wiiu" data-platform="wiiu" class="trigger selected" tabindex="0"><span>Wii U Communities</span></a>
    <a id="tab-3ds" data-platform="3ds" class="trigger" tabindex="0"><span>3DS Communities</span></a>
  </div>
  <div id="tab-wiiu-body" class="tab-body">
    <ul class="list community-list community-title-list">
<?php
        if($resultwiiu->num_rows == 0){
            showNoContent('No results found.');
            goto skipwiiu;
        }
        while($row = $resultwiiu->fetch_array()){?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php } skipwiiu: ?>
    </ul>
    </div>
    <div id="tab-3ds-body" class="tab-body none">
    <ul class="list community-list community-title-list">
        <?php
        if($result3ds->num_rows == 0){
            showNoContent('No results found.');
            goto skip3ds;
        }
        while($row = $result3ds->fetch_array()){?>
        <li id="community-<?= $row['id'] ?>" class="trigger" data-href="/communities/<?= $row['id'] ?>" tabindex="0">
          <span class="icon-container"><img src="<?= htmlspecialchars($row['icon'])?>" class="icon"></span>
          <div class="body">
              <a class="title" href="/communities/<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
              <?= getCommunityType($row['type']) ?>
          </div>
        </li>
        <?php } skip3ds: ?>
    </ul>
    </div>
  </div>