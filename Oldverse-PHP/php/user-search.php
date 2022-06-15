<?php
//needs a rewrite i think idk yet
$title = "Search";
require_once "lib/header.php";
if(!isset($_GET["query"])){
    header("Location: /");
}
//finally it works
$stmt = $db->prepare("SELECT id, username ,nickname, mii_hash, description, level FROM `users` WHERE username LIKE CONCAT('%', ?, '%') ORDER BY RAND()");
$stmt->bind_param("s", $_GET["query"]);
$stmt->execute();
$result = $stmt->get_result();
$stmt = $db->prepare("SELECT id, username, nickname, mii_hash, description, level FROM `users` ORDER BY RAND() LIMIT 3");
$stmt->execute();
$resultrandom = $stmt->get_result();
?>
<div class="body-content" id="search-top" data-region="USA">
    <div class="headline">
    <h2 class="headline-text">Search Users - "<?=htmlspecialchars($_GET["query"])?>"</h2>
    <form method="GET" action="/users" class="search">
      <input type="text" name="query" placeholder="Search Users" minlength="2" value="<?= htmlspecialchars($_GET['query']) ?>"/><input type="submit" value="q" title="Search"/>
    </form>
    </div>
   <ul class="list-content-with-icon-and-text arrow-list" id="following-list-content" data-next-page-url="">
    <?
    if($result->num_rows==0){
        showNoContent("No user was found.");
        goto skip;
    }
    while($row = $result->fetch_array()){
    ?>
    <li class="trigger" data-href="/users/<?= $row['username'] ?>">
        <a href="/users/<?= $row['username'] ?>" class="icon-container <?= $row['level']>0 ? 'official-user' : ''  ?>"><img src="<?= getAvatar($row['mii_hash'], 0) ?>" class="icon"></a>
        <div class="body">
            <p class="title">
                <span class="nick-name"><a href="/users/<?= $row['username'] ?>"><?= $row['nickname'] ?></a></span>
                <span class="id-name"><?= $row['username'] ?></span>
            </p>
            <p class="text"><?= $row['description'] ?></p>
        </div>
    </li>
    <? } ?>
    </ul>
    <? skip: ?>
<h2 class="label">Recommended Users</h2>
 <ul class="list-content-with-icon-and-text arrow-list" id="following-list-content" data-next-page-url="">
    <?
    if($resultrandom->num_rows==0){
        showNoContent("No user was found.");
        exit;
    }
    while($row = $resultrandom->fetch_array()){
    ?>
    <li class="trigger" data-href="/users/<?= $row['username'] ?>">
        <a href="/users/<?= $row['username'] ?>" class="icon-container <?= $row['level']>0 ? 'official-user' : ''  ?>"><img src="<?= getAvatar($row['mii_hash'], 0) ?>" class="icon"></a>
        <div class="body">
            <p class="title">
                <span class="nick-name"><a href="/users/<?= $row['username'] ?>"><?= $row['nickname'] ?></a></span>
                <span class="id-name"><?= $row['username'] ?></span>
            </p>
            <p class="text"><?= $row['description'] ?></p>
        </div>
    </li>
    <? } ?>
</ul>
