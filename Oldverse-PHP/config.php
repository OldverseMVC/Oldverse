<?php
//Oldverse configuration file
//DO NOT REMOVE THIS FILE!!! It will immediately break the website.
//Setup this to your liking, except PHP7_ENABLED (set it to true only if you're using PHP 7
//(which you shouldn't, at this point its outdated + vulnerable)
//reCaptcha and Discord login are both optional.


//database
const DB_HOST = "localhost"; //usally localhost
const DB_USER = "oldverse";
const DB_PASS = "";
const DB_NAME = "oldverse";

//general
const TIMEZONE = "America/New_York";
const SITE_NAME = "Oldverse";
const MEMO = SITE_NAME." is a service that lets you communicate with other players from around the world. It is accessible via PC, Wii U, and systems in the Nintendo 3DS family.";
const MAJOR = "1";
const MINOR = "3.21";
const LOGIN_NOTE = "This can be configured in config.php (const LOGIN_NOTE)";
const SIGNUP_NOTE = "This can also be configured in config.php (const SIGNUP_NOTE)";
const META_COLOR = "#27f011";
//enable yes or no referral keys
const PRIVATE_ENABLED = false;

//Change to true if you're using PHP 7.4 or older.
//Please, for the sake of god, use PHP 8.
//Please note that Oldverse only work with PHP 8.0, not 8.1 and older.
const PHP7_ENABLED = false;

// ReCAPTCHA settings
// set it to null for no recaptcha, else put on your site key & private key
const SITE_KEY = null;
const PRIVATE_KEY = null;

//Discord login
//be sure to add a slash to the end of the url
//if either CLIENT_ID or CLIENT_SECRET is set to "", the discord login functionnality is disabled
const WEBSITE_URL = "http://localhost/";
const CLIENT_ID = "";
const CLIENT_SECRET = "";

//Let it die (closes the websites and show up a website closed message)
const LET_IT_DIE = false;

//Forbidden IP list
/*
Insert forbidden IPs inside the list.

When a visitor come to check the website, the program will check if the incoming IP is in this list and if it does, it gets redirected to a special, unique page ;)
*/
const FORBIDDEN_IPS = [];

//If this setting is enabled, users will need to login to see Oldverse
const LOGIN_FORCED = false;

//As the title says
const SIGNUP_CLOSED = false;

// ----------------------EMOJIS------------------------

//This is a list in the following format: "emoji_name"=>"emoji_url"
//Seperate each entry with a comma.
//You use them like you would do on discord: :emoji_name:
const EMOJIS = ["oldverse"=>"/assets/img/favicon.ico"];
