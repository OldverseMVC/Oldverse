<?php
require_once "lib/adm-header.php";
if($user["level"] < 1 || !isset($_SESSION['token'])){
    exit("Access_Denied");
}?>
<div class="mainContainer">
    <div class="col-md-6 col-md-offset-3">
        <h1>Admin Panel - Reports</h1>
        <?php 
        if(isset($_GET['id'])){
            $action = $db->prepare("DELETE FROM reports WHERE id = ?");
            $action->bind_param('i', $_GET['id']);
            $action->execute();
            if(!$action->error){
                header("Location: /settings/admin/reports");
            }else {
                showError(500, "Error while trying to delete report /w id ".htmlspecialchars($_GET["id"]));
            }
        }
        $stmt = $db->prepare("SELECT * FROM reports");
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            //don't use showError(); please
            showNoContent("There are no reports at this time. <a href='/'>Back</a>");
            exit;
        }
        ?>
            <p>Finally finalized. Don't look at code backend, it's a fucking hell.</p><br>
            <?php
            while($row = $result->fetch_assoc()){
            $getuser = $db->prepare("SELECT username FROM users WHERE id = ?");
            $getuser->bind_param('i', $row['source']);
            $getuser->execute();
            if($getuser->error){
                showError(500, "An error occurred while trying to fetch reports. ".$getuser->error.""); // this is actually a case to use showError
                exit;
            }
            $resuser = $getuser->get_result();
            $rowuser = $resuser->fetch_assoc();
            if($row["type"] == 1){ // 1 is post, 2 is comment, 3 is profile. ?>
                <p><b> Report ID: <?=$row["id"]?></b> - <?=htmlspecialchars($rowuser["username"])?> is reporting <a href="/posts/<?=$row["target"]?>">this</a> <b>post.</b></p>
                <p>Report text: "<?=htmlspecialchars($row["report"])?>"</p>
                <a href="/settings/admin/reports?id=<?=$row["id"]?>">Delete report</a><br>
            <?php } else if($row["type"] == 2){ ?>
                <p><b> Report ID: <?=$row["id"]?></b> - <?=htmlspecialchars($rowuser["username"])?> is reporting <a href="/replies/<?=$row["target"]?>">this</a> <b>reply.</b></p>
                <p>Report text: "<?=htmlspecialchars($row["report"])?>"</p>
                <a href="/settings/admin/reports?id=<?=$row["id"]?>">Delete report</a><br>
            <?php } else if($row["type"] == 3){ 
            $getuser = $db->prepare("SELECT username FROM users WHERE id = ?");
            $getuser->bind_param('i', $row['source']);
            $getuser->execute();
            if($getuser->error){
                showError(500, "An error occurred while trying to fetch reports. ".$getuser->error.""); // this is actually a case to use showError
                exit;
            }
            $resuser = $getuser->get_result();
            $rowuser2 = $resuser->fetch_assoc();
            ?>
                <p><b> Report ID: <?=$row["id"]?></b> - <?=htmlspecialchars($rowuser["username"])?> is reporting <a href="/users/<?=$rowuser2["username"]?>">this</a> <b>profile.</b></p>
                <p>Report text: "<?=htmlspecialchars($row["report"])?>"</p>
                <a href="/settings/admin/reports?id=<?=$row["id"]?>">Delete report</a><br>
            <?php } ?>
            <?php } ?>
    </div>
</div>