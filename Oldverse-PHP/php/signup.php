<?php
$title = "Sign Up";
require_once "lib/header.php";
if(SIGNUP_CLOSED){
    showError(403, "Signups are closed. Contact an admin.");
}
if($_SERVER['REQUEST_METHOD']=="POST"){
    if(!empty(PRIVATE_KEY)) {
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => PRIVATE_KEY, 'response' => $_POST['g-recaptcha-response'], 'remoteip' => $_SERVER['REMOTE_ADDR']]));
        $response = curl_exec($ch);
        curl_close($ch);
        $responseJSON = json_decode($response);
        if($responseJSON->success === false) {
            $error = 'The reCAPTCHA was not solved correctly.';
            goto showForm;
        }
    }
    if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['cpassword']) || empty($_POST['nnid']) || empty($_POST['nickname'])){
        $error = "Some fields are empty.";
        goto showForm;
    }
    if(PRIVATE_ENABLED && empty($_POST['referral'])){
        $error = "Some fields are empty.";
        goto showForm;
    }
    if(!empty($_POST['nnid'])){
        $hash = file_get_contents("https://nnidlt.murilo.eu.org/api.php?output=hash_only&env=production&user_id=".$_POST['nnid']);
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
    if(!empty($_POST['referral'])){
        $stmt = $db->prepare("SELECT used FROM referralkey WHERE referralkey = ?");
        $stmt->bind_param('s', $_POST['referral']);
        $stmt->execute();
        if($stmt->error){
            $error = "There was an error while trying to fetch the referral key from the database.";
            goto showForm;
        }
        $result = $stmt->get_result();
        if($result->num_rows==0){
            $error = "Invalid referral key.";
            goto showForm;
        }
        $row = $result->fetch_array();
        if($row['used']==1){
            $error = "This referral key has already been used. Please ask for another one.";
            goto showForm;
        }
        $stmt = $db->prepare("UPDATE referralkey SET used = 1 WHERE referralkey = ?");
        $stmt->bind_param('s', $_POST['referral']);
        $stmt->execute();
        if($stmt->error){
            $error = "There was an error while trying to set the used flag on the referral key.";
            goto showForm;
        }
    }
    $stmt = $db->prepare("INSERT INTO `users`(`username`, `nickname`, `password`, `mii_hash`, `nnid`, `ip`, `flipnote_token`, `allows_online_status`) VALUES(?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssssss", $_POST['username'], $_POST['nickname'], $hash_pass, $hash, $_POST['nnid'], $_SERVER['REMOTE_ADDR'], rand(1, 2147483647));
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
<p style="text-align: center;"><?= SIGNUP_NOTE ?></p>
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
    <?php if(PRIVATE_ENABLED){ ?><p><b><i>Required. To have a referral key, <br>you must be friend with the people here, and ask someone<br> you know for a referral key.</i></b></p>
    <input type="text" placeholder="Referral key" name="referral" maxlength="69" requried><?php } ?>
    <?php if(!empty(SITE_KEY)) { ?><br>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <div class="g-recaptcha" style="display:inline-block" data-sitekey="<?=htmlspecialchars(SITE_KEY)?>"></div>
    <?php } ?>
    <div class="form-buttons">
        <input type="submit" value="Sign Up" class="black-button">
    </div>
    <br>
    <a href="/account/login">Already have an account?</a>
</form>
