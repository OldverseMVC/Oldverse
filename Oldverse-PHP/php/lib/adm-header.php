<?php
require_once "lib/connect.php";
if(isset($_SESSION['token'])){
    $user = getUser($_SESSION['token']);
}
?>
<!DOCTYPE html>
<html lang="en" data-sitecatalyst-suite-id="miiverseweb">
    <head>
        <title><?= isset($title) ? $title ." | ".SITE_NAME: SITE_NAME ?>_ADMIN</title>
        <link rel="stylesheet" href="/assets/css/admin.css">
        <link rel="shortcut icon" href="/assets/img/favicon.ico">
        <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/apple-touch-icon-144x144.png">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    </head>
    <body>
        <header>
            <div class="nabver navbar-fixed-top header header_lp1">
                <div class="headerLogo">
                    <a href="/settings/admin">
                        <img alt="_ADMIN" title="_ADMIN" src="/assets/img/menu-logo.png" width="130" height="38">
                    </a>
                </div>
            </div>
        </header>
        <div class="container-fluid">