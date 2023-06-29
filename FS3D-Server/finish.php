<?php
require_once "lib/connect.php";
ob_start();
?>
<html>
<head>
    <meta name="kwhistory-stack" content="clear-stack">
    <meta name="kwhistory-overwrite" content="http://<?= $_SERVER['SERVER_NAME'] ?>/finish.php">
</head>
<body>
    <fix>
        <kwmenubar href="*title" text="Exit" text_sound="return_top">
        <kwheadtextbox x="200" y="120" align="center" color="#3bcf1d" size="24" z="0" text="Oldverse" valign="center">
    </fix>
    <h1>Your post & flipnote has been uploaded!</h1>
    <p>Reload your activity feed to see it. ;)</p>
    <a href="main.php">Go to Oldverse companion app main menu</a>
</body>
</html>
<?php
header("Content-Length: ".ob_get_length());
ob_end_flush();
?>
