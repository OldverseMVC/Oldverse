<?php
ob_start();
require_once "lib/header.php";
?>
    <h1>What is this</h1>
    <p>This is an companion app to post posts to Oldverse with an embedded flipnote in it!</p>
    <h3>Why? Like look, there is 100 things more c00ler than that.</h3>
    <p>Because it is cool to post flips, no? Like you can upload a flipnote about idk, a song/a funny moment!</p>
</body>
</html>
<?php
header('Content-Length: '.ob_get_length());
ob_end_flush();
?>
