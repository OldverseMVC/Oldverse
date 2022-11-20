<?php
$title = "Edit community";
require_once "lib/header.php";
requireAuth();
$stmt = $db->prepare("SELECT owner, icon, banner, name, description, type, badge FROM communities WHERE id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
if($stmt->error){
    showError(500, "An error occured while fetching the community from the database.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    showError(404, "No community could be found with this ID.");
}
if(!isset($_GET['id'])){
    showError(403, "You need to specify a community ID.");
}
$row = $result->fetch_array();
if($user['id']!==$row['owner']){
    showError(403, "You cannot perform this action.");
}
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
    }
    if(!empty($_POST['banner'])){
        if(!preg_match('|(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', $_POST['banner'])){
            showError(400, 584694, 'Invalid banner URL.');
        }
        $exts = array('jpg', 'gif', 'png', 'webp', 'jpeg', 'bmp', 'svg');
        
        if(!in_array(strtolower(pathinfo($_POST['banner'], PATHINFO_EXTENSION)), $exts)) {
            showError(400, "Your icon URL is NOT an image. (or it is an data:image)");
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
    if(!empty($_POST['badge'])){
        if(strlen($_POST['badge'])>50){
            showError(400, "Your community badge is too large. (max: 50 characters)");
        }
    }else{
        $_POST['badge'] = null;
    }
    if(isset($_POST['type'])){
        if($_POST['type']<0 || $_POST['type']>2){
            showError(400, "Your community type is invalid.");
        }
    }
    $stmt = $db->prepare("UPDATE communities SET name = ?, description = ?, icon = ?, banner = ?, type = ?, badge = ? WHERE id = ?");
    $stmt->bind_param('ssssisi', $_POST['name'], $_POST['description'], $_POST['icon'], $_POST['banner'], $_POST['type'], $_POST['badge'], $_GET['id']);
    $stmt->execute();
    header("Location: /communities/".$_GET['id']);
}
?>
<h2 class="headline">Edit community</h2>
<form id="profile-settings-form" class="setting-form" method="post">
    <ul class="settings-list">
        <li class="setting-profile-comment">
            <p class="settings-label">Name</p>
            <input type="text" class="textarea-line url-form" name="name" placeholder="The name of your community." value="<?= htmlspecialchars($row['name']) ?>">
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Description</p>
            <textarea id="profile-text" class="textarea" name="description" maxlength="2000" placeholder="Write the community description here."><?= htmlspecialchars($row['description']) ?></textarea>
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Icon & banner image URL</p>
            <input type="text" class="textarea-line url-form" name="icon" placeholder="Icon URL" value="<?= htmlspecialchars($row['icon']) ?>">
            <input type="text" class="textarea-line url-form" name="banner" placeholder="Banner URL" value="<?= htmlspecialchars($row['banner']) ?>">
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Community Badge (optional)</p>
            <input type="text" class="textarea-line url-form" name="badge" placeholder="Badge name" maxlength="50" value="<?= htmlspecialchars($row['badge']) ?>">
        </li>
        <li class="setting-profile-comment">
            <p class="settings-label">Type</p>
            <select name="type">
                <option value="0" <?= $row['type']==0 ? "selected" : ""?>>Wii U Community</option>
                <option value="1" <?= $row['type']==1 ? "selected" : ""?>>3DS Community</option>
                <option value="2" <?= $row['type']==2 ? "selected" : ""?>>Wii U/3DS Community</option>
            </select>
        </li>
    </ul>
    <div class="form-buttons">
        <input type="submit" class="black-button apply-button" value="Go!">
    </div>
</form>    