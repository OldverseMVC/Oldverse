<?php
$title = "Admin Panel";
require_once "lib/header.php";
requireAuth();
if($user["level"] < 1){
    showError(403, "You're not allowed to access this page.");
}
?>
<h2 class="headline">Admin Panel</h2><div class="list my-menu-list">
    <a id="my-menu-settings-profile" class="scroll big-button" href='/settings/admin/reports'>View Reports</a>
    <a id="my-menu-settings-profile" class="scroll big-button" href='/settings/admin/accounts'>Manage Accounts</a>
    <a id="my-menu-settings-profile" class="scroll big-button" href='/settings/admin/site'>Site Settings</a>
    <a id="my-menu-logout" class="symbol" href='/my-menu'>Back</a>
</div>