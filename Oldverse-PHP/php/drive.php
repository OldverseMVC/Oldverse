<?php
$title = "OldDrive";
require_once "lib/header.php";
requireAuth();
$stmt = $db->prepare("SELECT id, name, url FROM files WHERE author = ? ORDER BY id DESC");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if(!is_dir(dirname(__DIR__)."/files/".$user['username']."/")){
    mkdir(dirname(__DIR__)."/files/".$user['username']."/");
}
$destination = dirname(__DIR__).'/files/'.$user['username'].'/';
if($_SERVER['REQUEST_METHOD']=='POST'){
    if($_FILES['file']['size'] > 0){
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $mime_type = mime_content_type($_FILES['file']['tmp_name']);

            $forbidden_file_types = ['application/x-httpd-php', 'text/html'];
            if (in_array($mime_type, $forbidden_file_types)) {
                showError(403, "File is forbidden, sorry.");
            }
            $destination = dirname(__DIR__).'/files/'.$user['username'].'/';
            if(file_exists($destination.$_FILES['file']['name'])){
                showError(400, "This file already exists.");
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'] , $destination.$_FILES['file']['name'])) {
                $stmt = $db->prepare("INSERT INTO files(name, url, author) VALUES(?, ?, ?)");
                $fullurl = '/files/'.$user['username'].'/'.$_FILES['file']['name'];
                $stmt->bind_param('ssi', $_FILES['file']['name'], $fullurl, $user['id']);
                $stmt->execute();
                header("Location: /olddrive");
            }else{
                showError(500, "An error occured while uploading.");
            }
        }else{
            showError(403, "Oh, too bad; try better next time! ;)");
        }
    }elseif(!empty($_POST['delete'])){
        $stmt = $db->prepare("SELECT name, author FROM files WHERE id = ?");
        $stmt->bind_param('i', $_POST['delete']);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows==0){
            showError(404, "File not found.");
        }
        $row = $result->fetch_array();
        if($row['author']!==$user['id']){
            showError(403, "You're not allowed to do this action.");
        }
        if(file_exists($destination.$row['name'])){
            $stmt = $db->prepare("DELETE FROM files WHERE id = ?");
            $stmt->bind_param('i', $_POST['delete']);
            $stmt->execute();
            unlink($destination.$row['name']);
            header("Location: /olddrive");
        }else{
            showError(400, "Uh oh... Seems like your file has been already deleted. Whoops!");
        }
    }else{
        showError(400, "No files were uploaded.");
    }
}
?>
<h1 class="headline">OldDrive</h1>
<div class="setting-form">
    <ul class="settings-list">
        <p>OldDrive is a basic drive to store files quickly & get 'em quickly.</p>
        <b>The following files are forbidden: .php files, .html files.</b>
        <h1 style="font-size: large;">Upload a file</h1>
        <form method="post" enctype="multipart/form-data">
        <div class="file-button-container">
            <span class="input-div">Everything is allowed, except HTML & PHP.</span>
            <input type="file" name="file" class="file-button">
        </div>            
        <div class="form-buttons">
            <input type="submit" class="button black-button" value="Upload">
        </div>
        </form>
        <li class="setting-profile-comment">
            <p>List of files</p>
        </li>
        <?php
        if($result->num_rows==0){
            showNoContent("No files was found.");
        }
        ?>
        <?php
        while($row = $result->fetch_array()){?>
        <li class="setting-profile-comment">
            <p style="font-size: large;"><?= htmlspecialchars($row['name']) ?></p>
            <a href="<?= htmlspecialchars($row['url']) ?>">Download file</a>
            <form method="post"><input type="hidden" name="delete" value="<?= $row['id'] ?>"><input type="submit" class="button" style="color: red;" value="Remove" class="a"></form>
        </li>
        <?php }
        ?>
    </ul>
</div>

