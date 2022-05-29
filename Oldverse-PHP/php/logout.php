<?php
$skip_t_check = true;
require_once "lib/connect.php";
session_destroy();
$stmt = $db->prepare("DELETE FROM `tokens` WHERE user = ?");
$stmt->bind_param('i', $row['id']);
$stmt->execute();
header("Location: ".$_GET['location']);