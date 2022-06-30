<?php
$title = "Tags Settings";
require_once "lib/header.php";
requireAuth();
$stmt = $db->prepare("SELECT id, name, value FROM tags WHERE created_by = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showError(500, 'An error occured while trying to fetch the profile tags.');
}
$result = $stmt->get_result();
if($_SERVER['REQUEST_METHOD']=='POST' && empty($_POST['id'])){
    if(!empty($_POST['name']) && !empty($_POST['value'])){
        $stmt = $db->prepare("INSERT INTO tags(name, value, created_by) VALUES(?, ?, ?)");
        $stmt->bind_param('ssi', $_POST['name'], $_POST['value'], $user['id']);
        $stmt->execute();
        if($stmt->error){
            showError(500, "An error occured while inserting the tag into the database.");
        }
        header("Location: /settings/tags");
    }else{
        showError(400, "Some fields are empty!");
    }
}
if($_SERVER['REQUEST_METHOD']=='POST' && empty($_POST['value'])){
    if(!empty($_POST['id'])){
        $stmt = $db->prepare("DELETE FROM tags WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        if($stmt->error){
            showError(500, "An error occured while deleting the tag from the database.");
        }
        header("Location: /settings/tags");
    }else{
        showError(400, "Some fields are empty!");
    }
}
?>
<h2 class="headline">Tags Settings</h2>
<div class="setting-form">
    <ul class="settings-list">
        <p class="note">Tags are profile tags that will, well, show up on your profile, next to you NNID, user ID, custom URL...</p>
        <p class="note"><strike>it's totally not for me to don't need to fuck up with custom tags anymore</strike></p>
        <h1 style="font-size: large;">Create a tag</h1>
        <form method="post">
            <p>Name</p>
            <input type="text" class="textarea-line url-form" name="name" placeholder="Name">
            <p>Value</p>
            <input type="text" class="textarea-line url-form" name="value" placeholder="Value">
            <div class="form-buttons">
                <input type="submit" class="black-button apply-button" value="Create the tag">
            </div>
        </form>
        <li class="setting-profile-comment">
            <p>List of tags</p>
        </li>
        <? if($result->num_rows!==0){
            while($row = $result->fetch_array()){?>
                <li class="setting-profile-comment">
                    <h1 style="font-size: large;"><?= htmlspecialchars($row['name'])?></h1>
                    <p><?= htmlspecialchars($row['value'])?></p>
                    <form method="post"><input type="hidden" name="id" value="<?= $row['id'] ?>"><input type="submit" class="button" style="color: red;" value="Remove" class="a"></form>
                </li>
        <? }} ?>
    </ul>
</div>
<? if($result->num_rows==0){ showNoContent("No profile tag was found."); } ?>