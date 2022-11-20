<?php
$is_guide = true;
$title = "Emoji List";
require_once "lib/header.php";
?>
<h2 class="headline">Emoji List</h2>
<div id="help" class="help-content">
<p>:D custom emojis time</p>
<h2>Emoji List</h2>
<?php foreach(EMOJIS as $key => $value){?>
<p>Emoji: <?= $key ?>, image: <img src="<?= $value ?>" style="width: 18px; height: 18px;"></p>
<?php  } ?>
<h2>How to place an emoji</h2>
<p>Simple; :emoji_name: (like discord)</p>
<h2>Can I add one?</h2>
<p>Contact website owner.</p>
</div>