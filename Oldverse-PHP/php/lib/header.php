<?php
require_once "lib/connect.php";
if(isset($_SESSION['token'])){
    $user = getUser($_SESSION['token']);
}
$is_identified_user = isset($is_identified_user) ? $is_identified_user : null;
$custom_id = isset($custom_id) ? $custom_id : null;
?>
<!DOCTYPE html>
<html lang="en" data-sitecatalyst-suite-id="miiverseweb">
    <head>
        <title><?= isset($title) ? $title ." | ".SITE_NAME: SITE_NAME ?></title>
        <script src="/assets/js/complete-en.js"></script>
        <link rel="stylesheet" href="/assets/css/offdevice.css">
        <link rel="shortcut icon" href="/assets/img/favicon.ico">
        <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/apple-touch-icon-144x144.png">
    </head>
    <body <?php if(!isset($_SESSION['username'])){ ?>id="<?php if(isset($is_guide)){ ?>help<?php } ?><?= !is_null($custom_id) ? ' '.$custom_id : '' ?>"  class="guest-top guest <?= $is_identified_user ? "identified_user" : "" ?>"<?php }else{ ?> class="<?= $is_identified_user ? "identified_user" : "" ?>" <?php } ?>id="<?php if(isset($is_guide)){ ?>help<?php } ?><?= !is_null($custom_id) ? ' '.$custom_id : '' ?>"data-token="<?= $_SESSION['token'] ?>">
        <div id="wrapper">
            <div id="sub-body">
                <?php
                if(!isset($_SESSION['username'])){
                ?>
                <menu id="">
                    <li id="global-menu-logo"><h1><a href="/"><img src="/assets/img/menu-logo.png" alt="Oldverse" width="200" height="55"></a></h1></li>
                    <li id="global-menu-login">
                        <form method="get" action="/account/login">
                            <input type="image" alt="Sign in" src="/assets/img/signin_base.png">
                        </form>
                    </li>
                </menu>
                <?php } ?>
                <?php
                if(isset($_SESSION['username'])){ ?>
                <menu id="global-menu">
                    <li id="global-menu-list">
                        <ul>
                            <li id="global-menu-logo"><h1><img src="/assets/img/menu-logo.png" alt="Oldverse" width="200" height="55"></h1></li>
                            <li id="global-menu-mymenu" class="<?= selected("my-userpage", $user['username']) ?>">
                                <a href="/users/<?= $_SESSION['username'] ?>">
                                    <span class="icon-container <?= $user['level'] > 0 ? 'official-user' : ''?>">
                                        <img src="<?= getAvatar($user['mii_hash'], 0) ?>" alt="User Page">
                                    </span>
                                    <span>User Page</span>
                                </a>
                            </li>
                            <li id="global-menu-feed" class="<?= selected("activity") ?>"><a href="/activity" class="symbol"><span>Activity Feed</span></a></li>
                            <li id="global-menu-community" class="<?= selected("communities") ?>"><a href="/" class="symbol"><span>Communities</span></a></li>
                            <li id="global-menu-news" class="<?= selected("notifications") ?>"><a href="/news/my_news" class="symbol"><span>Notifications</span></a></li>
                            <li id="global-menu-mymenu" class="<?= selected("my-menu") ?>"><a href="/my-menu" class="symbol"><span>My Menu</span></a></li>
                        </ul>
                    </li>
                </menu>
                <?php } ?>
            </div>
            <div id="footer">
                <p id="copyright">Oldverse <?= MAJOR.".".MINOR?></p>
            </div>
            <div id="main-body">