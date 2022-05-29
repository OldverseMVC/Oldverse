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
    <a id="my-menu-settings-profile" class="scroll big-button" onClick='window.location = "/settings/account"'>Account Settings</a>
    <?php if($adminrow["level"] > 1){ ?>
    <a id="my-menu-settings-profile" class="scroll big-button" onClick='window.location = "/settings/admin"'>Admin Panel</a>
    <?php } ?>
    <form action="/account/logout" method="get" id="my-menu-logout" class="symbol">
        <input type="hidden" name="location" value="/">
        <input type="submit" value="Log Out">
    </form>
</div>