<?php
$title = "Identified User Posts";
require_once "lib/header.php";
//Ugly but eh, who cares, it works now.
$stmt = $db->prepare("SELECT posts.id, community, created_by, flipnote, body, posts.url, screenshot, spoiler, feeling, created_at, mii_hash, nickname, level, icon, name, (SELECT COUNT(*) FROM empathies WHERE target = posts.id AND type = 0) AS empathy_count, (SELECT COUNT(*) FROM replies WHERE target = posts.id) AS reply_count, (SELECT UNIX_TIMESTAMP(posts.created_at)) AS timestamp FROM posts LEFT JOIN users ON created_by = users.id LEFT JOIN communities ON community = communities.id WHERE users.level > 6 ORDER BY posts.id DESC LIMIT 20");
$stmt->execute();
$result = $stmt->get_result();
?>
<div id="image-header-content">
  <span class="image-header-title">
    <span class="title">Posts from Verified Users</span>
    <span class="text">Get the latest news here!</span>
  </span>
  <img src="/assets/img/identified-user.png">
</div>
<div class="body-content" id="community-post-list" data-region="">
        <?php
        if($result->num_rows==0){
            showNoContent("No post was found.");
        }else{
            echo '<div class="list post-list">';
            while($row = $result->fetch_array()){
                require "elements/post.php";
            }
            echo '</div>';
        }
        ?>
</div>