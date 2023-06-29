<?php
ob_start();
require_once "lib/header.php";
?>
    <h1>Welcome to Oldverse companion app.</h1>
    <a href="http://<?= $_SERVER['SERVER_NAME'] ?>/post.php">Start posting</a>
    <br>
    <a href="http://<?= $_SERVER['SERVER_NAME'] ?>/info.php">More info</a>
</body>
</html>
<?php
header('Content-Length: '.ob_get_length());
ob_end_flush();
?>
