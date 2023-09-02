<html>
<head>
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/offdevice.css">
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/apple-touch-icon-144x144.png">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="title" content="Oldverse" />
    <meta name="description" content="Welcome back to Miiverse from 2014." />
    <meta name="abstract" content="Welcome back to Miiverse from 2014." />
    <meta name="keywords" content="miiverse, old, oldmiiverse, 2014, miiverseclone, mvc, socialnetwork" />
    <meta name="author" content="Oldverse Team" />
    <meta name="language" content="EN" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:title" content="Oldverse" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://<?= $_SERVER['SERVER_NAME'] ?>/" />
    <meta property="og:image" content="https://<?= $_SERVER['SERVER_NAME'] ?>/assets/img/favicon.ico" />
    <meta property="og:description" content="Welcome back to Miiverse from 2014." />
    <meta name="theme-color" content="#27f011">
</head>
<body class="guest" id="miiverse-will-reborn">
<div id="wrapper">
            <div id="sub-body">
                <menu id="global-menu">
                    <li id="global-menu-logo">
                        <h1><a href="/"><img src="/assets/img/menu-logo.png" alt="<?= SITE_NAME ?>" width="165" height="30"></a></h1>
                    </li>
                    <li id="global-menu-login">
                            <form id="login_form" action="/login" method="post">
                                <input type="image" alt="Sign in" src="/assets/img/signin_base.png">
                            </form>
                        </li>
                                    </menu>
            </div>
            <div id="main-body"><div id="special-offdevice">
    <div class="header">
        <div class="body-content">
            <div class="header-announce-wrapper"><h1>Welcome to <?= SITE_NAME ?>!</h1></div>
            <div class="header-image"><img class="header-image-size" src="/assets/img/welcome-banner.png"></div>
            <div class="social-button-area">
                <ul class="social-button">
                    <li>
                        <a href="/communities" class="twitter-share-button"><img src="/assets/img/signin_base.png"></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="contents-bg diary-bg">
        <div class="new-diary-image-bg body-content">
            <div class="new-diary-text-wrapper">
                <h2 class="new-diary-title">This is a message you can change in /php/welcome.php</h2>
                <p>You can change the image next to this text by replacing /assets/img/welcome-image-1.png by an image of your choice.</p>
                <p class="note">&#9670; Or you can change the path to the image directly in /php/welcome.php</p>
            </div>
            <img class="new-diary-image test-diary-screenshot-img" src="/assets/img/welcome-image-1.png">
        </div>
    </div>
    <div class="contents-bg bg1">
        <div class="body-content">
            <div class="new-album-text-wrapper">
                <h2 class="new-album-title">Lorem ipsum</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam non.</p>
                <p class="note">&#9670; This is a note! Yeah, it is!</p>
            </div>
            <img class="new-album-image test-album-screenshot-img" src="/assets/img/welcome-image-1.png">
        </div>
    </div>
    <div class="contents-bg bg2">
        <div class="other body-content">
            <h2>Other Information</h2>
            <ul>
                <li>This website is based off Oldverse, a FOSS clone of the old Miiverse website available on miiverse.nintendo.net from 2011 to 2015.</li>
                <li>It includes a lot more of functionnality than the original Miiverse, such as more customization options and user-made communities.</li>
                <li>The original source code (under AGPL v3 terms) is available here: <a href="https://github.com/OldverseMVC/Oldverse">https://github.com/OldverseMVC/Oldverse</a></li>
            </ul>
        </div>
    </div>
    <div class="footer">
        <a class="footer" href="http://discord.com/">miiverse better edition</a>
    </div>
</div>
            </div>
</body>
</html>
