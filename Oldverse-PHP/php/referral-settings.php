<?php
$title = "Referral Settings";
require_once "lib/header.php";
requireAuth();
$stmt = $db->prepare("SELECT id, referralkey, used FROM referralkey WHERE created_by = ? ORDER BY id DESC");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if($stmt->error){
    showError(500, 'An error occured while trying to fetch the referral keys.');
}
$result = $stmt->get_result();
if($_SERVER['REQUEST_METHOD']=='POST'){
    $referral = "oldv_".random_str();
    $stmt = $db->prepare("INSERT INTO referralkey(referralkey, created_by, used) VALUES(?, ?, 0)");
    $stmt->bind_param('si', $referral, $user['id']);
    $stmt->execute();
    if($stmt->error){
        showError(500, "An error occured while inserting the referral key into the database.");
    }
    header("Location: /settings/referral");
}
?>
<h2 class="headline">Referral Settings</h2>
<div class="setting-form">
    <ul class="settings-list">
        <p class="note">Referral keys can be used to invite other people to the service. WARNING: if the user you invite get banned, you get banned too.</p>
        <h1 style="font-size: large;">Create a referral key</h1>
        <form method="post">
            <div class="form-buttons">
                <input type="submit" class="black-button apply-button" value="Create the referral key">
            </div>
        </form>
        <li class="setting-profile-comment">
            <p>List of referral keys</p>
        </li>
        <?php  if($result->num_rows!==0){
            while($row = $result->fetch_array()){?>
                <li class="setting-profile-comment">
                    <h1 style="font-size: large;"><?= htmlspecialchars($row['referralkey'])?></h1>
                    <?= $row['used']==1 ? '<p style="color: red;">Used</p>' : '<p>Not used</p>'?>
                </li>
        <?php  }} ?>
    </ul>
</div>
<?php  if($result->num_rows==0){ showNoContent("No referral key was found."); } ?>