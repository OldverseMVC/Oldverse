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
const MEMO = SITE_NAME." is a service that lets you communicate with other players from around the world. It is accessible via PC, Wii U, and systems in the Nintendo 3DS family.";
const MAJOR = "1";
const MINOR = "3.00";
const LOGIN_NOTE = "please don't rehost";

//Forbidden IP list
/*
Insert forbidden IPs inside the list.

When a visitor come to check the website, the program will check if the incoming IP is in this list and if it does, it gets redirected to a special, unique page ;)
*/
const FORBIDDEN_IPS = [];

//If this setting is enabled, users will need to login to see Oldverse
const LOGIN_FORCED = true;

//As the title says
const SIGNUP_CLOSED = false;

// ----------------------EMOJIS------------------------

//In this category you can setup custom emojis that can be sent in a message like this :emojiname: (like discord) (18x18)
const EMOJIS = ["oldverse"=>"/assets/img/favicon.ico"];
