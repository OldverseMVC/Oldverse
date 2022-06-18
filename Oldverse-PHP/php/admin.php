<?php
require_once "lib/adm-header.php";
if(!isset($_SESSION['token'])){
?>
<div class="mainContainer">
    <div class="col-md-6 col-md-offset-3">
        <div class="form-signin">
            <div class="text-center">
                <img src="/assets/img/apple-touch-icon-144x144.png">
            </div>
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <a href="/account/login" class="btn btn-primary btn-block">Login with an Oldverse account</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?
}else{?>
<div class="mainContainer">
    <div class="col-md-6 col-md-offset-3">
        <h1>Oldverse Administration</h1>
        <a href="/">Home</a>
        <?
        if($user['level']<1){?>
            <p class="red">You don't have any admin permissions. Access denied.</p>
        <?}else{ ?>
        <p>Welcome to Oldverse admin panel! This is mostly as WIP, but things are getting added! Hope you'll enjoy it....</p>
        <h2>List of admin actions</h2>
        <ul>
            <li><a href="/settings/admin/accounts">Account/Posts management</a></li>
            <li><a href="/settings/admin/reports">Reports management</a></li>
        </ul>
        <? } ?>
    </div>
</div>
<?}
?>