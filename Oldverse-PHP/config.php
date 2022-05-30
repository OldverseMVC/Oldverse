<?php
//Oldverse settings.

//database
const DB_HOST = "localhost"; //usally localhost
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "oldverse";

//general
const TIMEZONE = "America/New_York";
const SITE_NAME = "Oldverse";
const MEMO = "".SITE_NAME." is a service that lets you communicate with other players from around the world. It is accessible via PC, Wii U, and systems in the Nintendo 3DS family.";
const MAJOR = "beta";
const MINOR = "1.0";

//Forbidden IP list
/*
Insert forbidden IPs inside the list.

When a visitor come to check the website, the program will check if the incoming IP is in this list and if it does, it gets redirected to a special, unique page ;)
*/
const FORBIDDEN_IPS = [];
