<?php
require_once "lib/connect.php";
requireAuth();
if($_SERVER['REQUEST_METHOD']=='POST'){
    $user = getUser($_SESSION['token']);
    if(!empty($_POST['profile_comment'])){
        if(strlen($_POST['profile_comment']) > 1000){
            showJSONError(400, 564546, 'The profile comment cannot exceed 1000 characters.');
        }
        $stmt = $db->prepare("UPDATE users SET description = ? WHERE id = ?");
        $stmt->bind_param('si', $_POST['profile_comment'], $user['id']);
        $stmt->execute();
    }
}
$title = "Profile Settings";
require_once "lib/header.php";
$stmt = $db->prepare("SELECT description FROM users WHERE id = ?");
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
  <p>Yeah, it feels kinda empty (for now). More is coming in the next days.</p>
  <ul class="settings-list">
    <li class="setting-profile-comment">
      <p class="settings-label">Profile Comment</p>
      <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="1000" placeholder="Write about yourself here."><?= $row['description'] ?></textarea>
    </li>
    
  </ul>
  <div class="form-buttons">
    <input type="submit" class="black-button apply-button" value="Save Settings">
  </div>
</form>