<?php
require_once "lib/connect.php";
requireAuth();
if($_SERVER['REQUEST_METHOD']=='POST'){
    $user = getUser($_SESSION['token']);
    if(!empty($_POST['profile_comment'])){
        if(strlen($_POST['profile_comment']) > 1000){
            showJSONError(400, 5644546, 'The profile comment cannot exceed 1000 characters.');
        }
    }
    if(!empty($_POST['nnid'])){
        $hash = file_get_contents("https://pf2m.com/hash/".$_POST['nnid']);
        if(!$hash){
            showJSONError(404, 4844948, "Your NNID could not be found/an error occured while fetching the Mii.");
        }
    }else{
        showJSONError(400, 5564489, 'You must precise an NNID.');
    }
    if(!empty($_POST['url'])){
        if(!preg_match('|(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', $_POST['url'])){
            showJSONError(400, 584694, 'Invalid URL.');
        }
        if(strlen($_POST['url']) > 64){
            showJSONError(400, 5458488, 'The custom URL cannot exceed 64 characters.');
        }
    }
    if(!empty($_POST['cpass']) && !empty($_POST['npass'])){
        if(!password_verify($_POST['cpass'], $user['password'])){
            showJSONError(400, 4454878, 'Invalid password.');
        }
        $hash_pass = password_hash($_POST['npass'], PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hash_pass, $user['id']);
        $stmt->execute();
    }
    $stmt = $db->prepare("UPDATE users SET description = ?, nnid = ?, mii_hash = ?, url = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $_POST['profile_comment'], $_POST['nnid'], $hash, $_POST['url'], $user['id']);
    $stmt->execute();
}
$title = "Profile Settings";
require_once "lib/header.php";
$stmt = $db->prepare("SELECT description, nnid, url, password FROM users WHERE id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showError(500, 'An error occured while grabbing settings.');
}
$result = $stmt->get_result();
$row = $result->fetch_array();
?>
<h2 class="headline">Profile Settings</h2>
    <form id="profile-settings-form" class="setting-form" method="post" action="/settings/profile">
  <ul class="settings-list">
    <li class="setting-profile-comment">
      <p class="settings-label">Profile Comment</p>
      <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="1000" placeholder="Write about yourself here."><?= $row['description'] ?></textarea>
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Nintendo Network ID</p>
      <input type="text" class="textarea-line url-form" name="nnid" placeholder="NNID" value="<?= $row['nnid'] ?>" maxlength="16">
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Custom URL</p>
      <input type="text" class="textarea-line url-form" name="url" placeholder="This will show up on your profile." value="<?= $row['url'] ?>" maxlength="64">
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Change Password</p>
      <input type="password" class="textarea-line url-form" name="cpass" placeholder="Current password">
      <br>
      <br>
      <input type="password" class="textarea-line url-form" name="npass" placeholder="New password">
    </li>
  </ul>
  <div class="form-buttons">
    <input type="submit" class="black-button apply-button" value="Save Settings">
  </div>
</form>