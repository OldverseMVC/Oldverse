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
        $hash = file_get_contents("https://nnidlt.murilo.eu.org/api.php?output=hash_only&env=production&user_id=".$_POST['nnid']);
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
    if(empty($_POST['nickname'])){
        showJSONError(400, 5156458, 'An nickname cannot be empty.');
    }
    if(isset($_POST['allows-online-status'])){
        if($_POST['allows-online-status']>1 || $_POST['allows-online-status']<0){
            showJSONError(400, 6565655, 'Invalid value for your online status preference.');
        }
    }else{
        showJSONError(400, 4894988, 'Your online status preference cannot be empty.');
    }
    if(!empty($_POST['nick-color'])){
        if(!preg_match('|#([a-zA-Z0-9_-]{3,6})|', $_POST['nick-color'], $matches)){
            showJSONError(400, 5925928, "Your nickname color isn't valid. Well played kid, next time try better!");
        }
    }else{
        $_POST['nick-color'] = "#000";
    }
    $stmt = $db->prepare("UPDATE users SET description = ?, nnid = ?, mii_hash = ?, url = ?, nickname = ?, allows_online_status = ?, nick_color = ? WHERE id = ?");
    $stmt->bind_param('sssssisi', $_POST['profile_comment'], $_POST['nnid'], $hash, $_POST['url'], $_POST['nickname'], $_POST['allows-online-status'], $_POST['nick-color'], $user['id']);
    $stmt->execute();
}
$title = "Profile Settings";
require_once "lib/header.php";
$stmt = $db->prepare("SELECT nickname, description, nnid, url, flipnote_token, password, favorite, allows_online_status, nick_color FROM users WHERE id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showError(500, 'An error occured while grabbing settings.');
}
$result = $stmt->get_result();
$row = $result->fetch_array();
if(isset($row['favorite'])){
    $stmt = $db->prepare("SELECT screenshot FROM posts WHERE id = ?");
    $stmt->bind_param('i', $row['favorite']);
    $stmt->execute();
    if($stmt->error){
        showError(500, 'An error occured.');
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        $row['favorite']=null;
    }
    $prow = $result->fetch_array();
}
?>
<h2 class="headline">Profile Settings</h2>
    <form id="profile-settings-form" class="setting-form" method="post" action="/settings/profile">
  <ul class="settings-list">
    <p><b>Personal Flipnote Token (to use in the <a href="/patch">companion app</a>): <?= htmlspecialchars($row['flipnote_token']) ?></b></p>
    <li class="setting-profile-comment">
      <p class="settings-label">Nickname</p>
      <input type="text" class="textarea-line url-form" name="nickname" placeholder="Nickname" value="<?= htmlspecialchars($row['nickname']) ?>">
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Profile Comment</p>
      <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="1000" placeholder="Write about yourself here."><?= htmlspecialchars($row['description']) ?></textarea>
    </li>
    <li class="setting-profile-post">
      <p class="settings-label">Favorite Post</p>
      <p class="note">You can set one of your own screenshot posts as your favorite from the settings button on that post.</p>
      <? if(!empty($row['favorite'])){ ?>
      <div class="select-content">
        <button id="profile-post" type="button" class="submit"><img src="<?= htmlspecialchars($prow['screenshot'])?>"><span class="symbol">Remove</span></button>
	   </div>
	   <? } ?>
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Nintendo Network ID</p>
      <input type="text" class="textarea-line url-form" name="nnid" placeholder="NNID" value="<?= htmlspecialchars($row['nnid']) ?>" maxlength="16">
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Custom URL</p>
      <input type="text" class="textarea-line url-form" name="url" placeholder="This will show up on your profile." value="<?= htmlspecialchars($row['url']) ?>" maxlength="64">
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Nickname color</p>
      <input type="color" name="nick-color" value="<?= htmlspecialchars($row['nick_color']) ?>">
    </li>
    <li class="setting-profile-comment">
      <p class="settings-label">Allow to display your online status</p>
      <p class="note">This setting, if set to Yes, will display "Last Seen" on your profile main page.</p>
      <select name="allows-online-status">
          <option value="1" <?= $row['allows_online_status']==1 ? 'selected': '' ?>>Yes</option>
          <option value="0"<?= $row['allows_online_status']==0 ? 'selected': '' ?>>No</option>
      </select>
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