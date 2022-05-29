<?php
require_once "lib/connect.php";
if($_SERVER['REQUEST_METHOD']!=="POST"){
    showJSONError(403, 4034003, "You must use a POST request.");
}
if(!isset($_SESSION['token'])){
    showJSONError(403, 5845545, 'You must be logged in to perform this action.');
}
if($_SESSION['token'] != $_POST['token']){
    showJSONError(403, 4034003, 'The CSRF check failed. Try logging out and logging back in.');
}
if(!isset($_POST["type"]) || !isset($_POST["body"])){
    showJSONError(404, 4041337, 'Missing parameters');
}
if(!($_POST["type"] == 1 || $_POST["type"] == 2 || $_POST["type"] == 3)){
    showJSONError(404, 4041337, 'Missing parameters');    
}
$user = getUser($_SESSION['token']);
$report = $db->prepare("INSERT INTO reports (`id`, `type`, `source`, `target`, `report`) VALUES (NULL, ?, ?, ?, ?);");
$report->bind_param("iiis", $_POST["type"], $user["id"], $_GET["id"], $_POST["body"]);
$report->execute();
if($report->error){
    showJSONError(500, 500100, 'An error occurred while trying to submit your report. '.$report->error.'');
} else {
    echo "";
}
?>