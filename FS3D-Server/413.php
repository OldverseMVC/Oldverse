<?php
require_once "lib/connect.php";
ob_start();
?>
<html>
<body>
    <fix>
        <kwmenubar href="*title" text="Exit" text_sound="return_top">
        <kwheadtextbox x="200" y="120" align="center" color="#3bcf1d" size="24" z="0" text="Oldverse" valign="center">
    </fix>
    <h1>Error</h1>
    <p>Seems like your flipnote is too large for the server current configuration, and refuses to upload it. Please upload a less larger one.</p>
    <a href="main.php">Go to Oldverse companion app main menu</a>
</body>
</html>
<?php
header("Content-Length: ".ob_get_length());
ob_end_flush();
?>
