<?php
require_once "lib/connect.php";

if(isset($_GET['linkplz'])){
    $_SESSION["link_d"] = "yes";
}
if(!isset($_GET['code'])){
    header("Location: https://discord.com/api/oauth2/authorize?client_id=".CLIENT_ID."&redirect_uri=".urlencode(WEBSITE_URL)."discord-api&response_type=code&scope=identify");
    exit;
}
$params=['client_id'=>CLIENT_ID, 'client_secret'=>CLIENT_SECRET, 'grant_type' => 'authorization_code', 'code' => $_GET['code'], 'redirect_uri' => WEBSITE_URL.'discord-api'];
$defaults = array(
CURLOPT_URL => "https://discord.com/api/v10/oauth2/token",
CURLOPT_POST => true,
CURLOPT_POSTFIELDS => $params,
CURLOPT_RETURNTRANSFER => true
);
$ch = curl_init();
curl_setopt_array($ch, $defaults);
$api = curl_exec($ch);
curl_close($ch);
$apidecoded = json_decode($api, true);
if(isset($apidecoded['error'])){
    exit("An error occured while connecting to Discord.");
}
$defaults = array(
CURLOPT_RETURNTRANSFER => true,
CURLINFO_HEADER_OUT => true,
CURLOPT_URL => "https://discord.com/api/v10/oauth2/@me",
CURLOPT_HTTPHEADER => ['Authorization: Bearer '.(string)$apidecoded['access_token']],
);
$ch = curl_init();
curl_setopt_array($ch, $defaults);
$api = curl_exec($ch);
curl_close($ch);
$apidecoded = json_decode($api, true);
if(isset($apidecoded['error'])){
    exit("An error occured while retrieving your user from Discord.");
}
if(isset($_SESSION['link_d'])){
    requireAuth();
    $user = getUser($_SESSION['token']);
    $stmt = $db->prepare("UPDATE users SET discordid = ? WHERE id = ?");
    $stmt->bind_param('si', $apidecoded['user']['id'], $user['id']);
    $stmt->execute();
    header("Location: /settings/profile");
    exit;
}
$stmt = $db->prepare("SELECT id, level, username FROM users WHERE discordid = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('s', $apidecoded['user']['id']);
$stmt->execute();
if($stmt->error){
    exit("An error occured while logging in.");
}
$result = $stmt->get_result();
if($result->num_rows==0){
    exit("No account is associated with this Discord account.");
}
$row2 = $result->fetch_array();
$token = rand(1, 2147483647);
$stmt = $db->prepare("INSERT INTO `tokens`(user, token) VALUES (?, ?)");
$stmt->bind_param("is", $row2['id'], $token);
$stmt->execute();
if($stmt->error){
    exit("An error occured while logging in.");
}
$_SESSION['token'] = $token;
$_SESSION['username'] = $row2['username'];
$_SESSION['level'] = $row2['level'];
header("Location: /");
?> 