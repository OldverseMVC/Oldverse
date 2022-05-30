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
    $stmt = $db->prepare("UPDATE users SET description = ?, nnid = ?, mii_hash = ? WHERE id = ?");
    $stmt->bind_param('sssi', $_POST['profile_comment'], $_POST['nnid'], $hash, $user['id']);
    $stmt->execute();
}
$title = "Profile Settings";
require_once "lib/header.php";
$stmt = $db->prepare("SELECT description, nnid FROM users WHERE id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showError(500, 'An error occured while grabbing settings.');
}
$result = $stmt->get_result();
$row = $result->fetch_array();
?>
<h2 class="headline">Profile Settings</h2>
    <form id="profile-settings-form" class="setting-form" method="post" action="/settings/account">
  <ul class="settings-list">
    <li class="setting-profile-comment">
      <p class="settings-label">Profile Comment</p>
      <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="1000" placeholder="Write about yourself here."><?= $row['description'] ?></textarea>
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Nintendo Network ID</p>
      <input type="text" class="textarea-line url-form" name="nnid" placeholder="NNID" value="<?= $row['nnid'] ?>" maxlength="16">
    </li>
    
  </ul>
  <div class="form-buttons">
    <input type="submit" class="black-button apply-button" value="Save Settings">
  </div>
</form>