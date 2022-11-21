<?php
$title = "My menu";
require_once "lib/header.php";
requireAuth();
if(isset($_SESSION["username"])){
    $adminchk = $db->prepare("SELECT level FROM users WHERE username = ?");
    $adminchk->bind_param("s", $_SESSION["username"]);
    $adminchk->execute();
    $adminres = $adminchk->get_result();
    $adminrow = $adminres->fetch_assoc();
}
?>
<h2 class="headline">User Menu</h2><div class="list my-menu-list">
    <a id="my-menu-settings-profile" class="scroll big-button" href="/settings/profile">Profile Settings</a>
    <a id="my-menu-settings-account" class="scroll big-button" href="/settings/tags">Tags Settings</a>
    <a id="my-menu-settings-account" class="scroll big-button" href="/settings/referral">Referral settings</a>
    <a id="my-menu-settings-account" class="scroll big-button" href="/olddrive">OldDrive</a>
    <?php if($adminrow["level"] > 1){ ?>
    <a id="my-menu-settings-profile" class="scroll big-button" href="/settings/admin">Admin Panel</a>
    <?php } ?>
    <a id="my-menu-profile" class="scroll big-button" href="/patch"><?= SITE_NAME ?> companion app for 3DS</a>
    <a id="my-menu-settings-account" class="scroll big-button" href="/emoji-list">Emoji List</a>
    <form action="/account/logout" method="get" id="my-menu-logout" class="symbol">
        <input type="hidden" name="location" value="/">
        <input type="submit" value="Log Out">
    </form>
</div>