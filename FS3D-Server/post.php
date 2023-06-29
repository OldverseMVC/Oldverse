<?php
ob_start();
require_once "lib/header.php";
?>
    <h1>Login via your personal flipnote token</h1>
    <a hreftype="post_keyboard(10,Type it here,CodeWord,)" href="post_2.php">Personal Flipnote Token (click to type)</a>
</body>
</html>
<?php
header('Content-Length: '.ob_get_length());
ob_end_flush();
?>
