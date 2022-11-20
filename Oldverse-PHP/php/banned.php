<?php 
require_once "lib/connect.php";
requireAuth();
$user = getUserID($_SESSION['token']);
$stmt = $db->prepare("SELECT reason FROM ban WHERE target = ?");
$stmt->bind_param('i', $user);
$stmt->execute();
if($stmt->error){
    echo "ohno ERORORRFERIOGJEQIGHIERJGIJERISU";
    exit;
}
$result = $stmt->get_result();
if($result->num_rows==0){
    header("Location: /");
}
$row = $result->fetch_array();
?>
<audio controls autoplay hidden>
    <source src="/assets/brugers.wav" type="audio/mpeg">
</audio>
<h1 class="headline">when sumbody steal yoe weed</h1>
<div class="no-content">wp, you got banned for this reason stupjid: <?= $row['reason'] ?></p></div>
<a href="/">check to see if yoe weed had ben return or expire 😂😂😂</a>