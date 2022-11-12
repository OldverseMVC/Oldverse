<?php
$title = "Login";
require_once "lib/header.php";
if($_SERVER['REQUEST_METHOD']=="POST"){
    if(empty($_POST['username']) || empty($_POST['password'])){
        $error = "Some fields are empty.";
        goto showForm;
    }
    $stmt = $db->prepare("SELECT id, password, level FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    if($stmt->error){
        $error = "An error occured while fetching your user from the database.";
        goto showForm;
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        $error = "No user was found with this username.";
        goto showForm;
    }
    $row = $result->fetch_array();
    if(!password_verify($_POST['password'], $row['password'])){
        $error = "Invalid password.";
        goto showForm;
    }
    $stmt = $db->prepare("DELETE FROM `tokens` WHERE user = ?");
    $stmt->bind_param('i', $row['id']);
    $stmt->execute();
    $token = rand(1, 2147483647);
    $stmt = $db->prepare("INSERT INTO `tokens`(user, token) VALUES (?, ?)");
    $stmt->bind_param("is", $row['id'], $token);
    $stmt->execute();
    if($stmt->error){
        $error = "An error occured while inserting your token inside the database.";
        goto showForm;
    }
    $_SESSION['token'] = $token;
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['level'] = $row['level'];
    header("Location: /");
}
showForm:
?>
<h2 class="welcome-message">Login</h2>
<p style="text-align: center;"><?= LOGIN_NOTE ?></p>
<form method="post" class="center">
    <?php if(!empty($error)){?>
    <p style="color: red;"><b><?= $error ?></b></p>
    <?php } ?>
    <input type="text" placeholder="Username" name="username">
    <input type="password" placeholder="Password" name="password">
    <div class="form-buttons">
        <input type="submit" value="Login" class="black-button">
    </div>
    <br>
    <? if(!SIGNUP_CLOSED){ ?><a href="/account/signup">No account yet?</a><? } ?>
    <br><a href="/discord-api">Or sign in using Discord</a> 
</form>