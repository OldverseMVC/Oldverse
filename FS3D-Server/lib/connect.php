<?php
const DB_HOST = "localhost"; //usally localhost
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "oldverse";

//image settings
const OLDVERSE_INSTANCE_PATH = '/path/to/oldverse';

//connect!
$db = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); // DB_NAME

if(!empty($_SERVER['HTTP_USER_AGENT'])){
    exit("Access Forbidden.");
}
