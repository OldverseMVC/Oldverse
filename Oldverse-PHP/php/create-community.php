<?php
$title = "Create community";
require_once "lib/header.php";
requireAuth();
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(empty($_POST['name']) || empty($_POST['description']) || empty($_POST['icon']) || empty($_POST['banner']) || !isset($_POST['type'])){
        showError(400, "Some fields are empty.");
    }
    if(!empty($_POST['icon'])){
        if(!preg_match('|(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', $_POST['icon'])){
            showError(400, 'Invalid icon URL.');
        }
        $exts = array('jpg', 'gif', 'png', 'webp', 'jpeg', 'bmp', 'svg');
        
        if(!in_array(strtolower(pathinfo($_POST['icon'], PATHINFO_EXTENSION)), $exts)) {
            showError(400, "Your icon URL is NOT an image. (or it is an data:image)");
        }
        $chk = file_get_contents($_POST['icon']);
        if(!$chk){
            showError(400, "It seems your icon doesn't exist.");
        }
    }
    if(!empty($_POST['banner'])){
        if(!preg_match('|(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', $_POST['banner'])){
            showError(400, 584694, 'Invalid banner URL.');
        }
        $exts = array('jpg', 'gif', 'png', 'webp', 'jpeg', 'bmp', 'svg');
        
        if(!in_array(strtolower(pathinfo($_POST['banner'], PATHINFO_EXTENSION)), $exts)) {
            showError(400, "Your icon URL is NOT an image. (or it is an data:image)");
        }
        $chk = file_get_contents($_POST['banner']);
        if(!$chk){
            showError(400, "It seems your icon doesn't exist.");
        }
    }
    if(!empty($_POST['name'])){
        if(strlen($_POST['name'])>200){
            showError(400, "Your community name is too large. (max: 200 characters)");
        }
    }
    if(!empty($_POST['description'])){
        if(strlen($_POST['description'])>2000){
            showError(400, "Your community name is too large. (max: 2000 characters)");
        }
    }
    if(isset($_POST['type'])){
        if($_POST['type']<0 || $_POST['type']>2){
            showError(400, "Your community type is invalid.");
        }
    }
    $stmt = $db->prepare("INSERT INTO communities(name, description, icon, banner, permissions, type, featured, is_flipnote, owner) VALUES(?, ?, ?, ?, 0, ?, 0, 0, ?)");
    $stmt->bind_param('ssssii', $_POST['name'], $_POST['description'], $_POST['icon'], $_POST['banner'], $_POST['type'], $user['id']);
    $stmt->execute();
    $result = $db->query("SELECT id FROM communities ORDER BY id DESC LIMIT 1");
    $row = $result->fetch_array();
    header("Location: /communities/".$row['id']);
}
?>
<h2 class="headline">Create community</h2>
<form id="profile-settings-form" class="setting-form" method="post">
    <ul class="settings-list">
        <li class="setting-profile-comment">
            <p class="settings-label">Name</p>
            <input type="text" class="textarea-line url-form" name="name" placeholder="The name of your community.">
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Description</p>
            <textarea id="profile-text" class="textarea" name="description" maxlength="2000" placeholder="Write the community description here."></textarea>
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Icon & banner image URL</p>
            <input type="text" class="textarea-line url-form" name="icon" placeholder="Icon URL">
            <input type="text" class="textarea-line url-form" name="banner" placeholder="Banner URL">
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Type</p>
            <select name="type">
                <option value="0">Wii U Community</option>
                <option value="1">3DS Community</option>
                <option value="2">Wii U/3DS Community</option>
            </select>
        </li>
    </ul>
    <div class="form-buttons">
        <input type="submit" class="black-button apply-button" value="Go!">
    </div>
</form>    