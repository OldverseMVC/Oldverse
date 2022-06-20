<?php
$title = "Sign Up";
require_once "lib/header.php";
if(SIGNUP_CLOSED){
    showError(403, "Signups are closed. Contact an admin.");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
    if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['cpassword']) || empty($_POST['nnid']) || empty($_POST['nickname'])){
        $error = "Some fields are empty.";
        goto showForm;
    }
    if(!empty($_POST['nnid'])){
        $hash = file_get_contents("https://pf2m.com/hash/".$_POST['nnid']);
        if(!$hash){
            $error = "Your NNID could not be found/an error occured while fetching the Mii.";
            goto showForm;
        }
    }
    if(!empty($_POST['password'])){
        if($_POST['password'] !== $_POST['cpassword']){
            $error = "Passwords don't match.";
            goto showForm;
        }
        $hash_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }
    if(!empty($_POST['username'])){
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $_POST['username']);
        $stmt->execute();
        if($stmt->error){
            $error = "An error occured while checking if any user with your username exists.";
            goto showForm;
        }
        $result = $stmt->get_result();
        if($result->num_rows!==0){
            $error = "An user already exists with that username.";
            goto showForm;
        }
    }
    $stmt = $db->prepare("INSERT INTO `users`(`username`, `nickname`, `password`, `mii_hash`, `nnid`, `ip`) VALUES(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['username'], $_POST['nickname'], $hash_pass, $hash, $_POST['nnid'], $_SERVER['REMOTE_ADDR']);
    $stmt->execute();
    if($stmt->error){
        $error = "An error occured while inserting your user into the database.";
        goto showForm;
    }
    header("Location: /account/login");
}
showForm:
?>
<h2 class="welcome-message">Sign Up</h2>
<p style="text-align: center;">Time to shine, baby!</p>
<form method="post" class="center">
    <?php if(!empty($error)){?>
    <p style="color: red;"><b><?= $error ?></b></p>
    <?php } ?>
    <input type="text" placeholder="Username" name="username" required>
    <input type="text" placeholder="Nickname" name="nickname" required>
    <input type="password" placeholder="Password" name="password" required>
    <input type="password" placeholder="Confirm Password" name="cpassword" required>
    <p><b><i>This is required. You MUST have a valid NNID.</i></b></p>
    <input type="text" placeholder="Nintendo Network ID (NNID)" name="nnid" maxlength="16" requried>
    <div class="form-buttons">
        <input type="submit" value="Sign Up" class="black-button">
    </div>
    <br>
    <a href="/account/login">Already have an account?</a>
</form>