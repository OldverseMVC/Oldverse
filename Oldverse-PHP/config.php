<?php
//Oldverse settings.

//database
const DB_HOST = "localhost"; //usally localhost
const DB_USER = "oldverse";
const DB_PASS = "";
const DB_NAME = "oldverse";

//general
const TIMEZONE = "America/New_York";
const SITE_NAME = "Oldverse";
const MEMO = SITE_NAME." is something you shouldnt join";
const MAJOR = "1";
const MINOR = "3.20";
const LOGIN_NOTE = "have fun, warning: it will die in a few weeks";
const SIGNUP_NOTE = "don't leave please";
const META_COLOR = "#27f011";
//enable yes or no referral keys
const PRIVATE_ENABLED = false;

//Change to true if you're using PHP 7.4 or older.
/* VERY VERY IMPORTANT NOTE */
//Starting from when my host will migrate to PHP 8,
//PHP 7 support for Oldverse will drop.
//While it might work with PHP 7 with a few mods on the codebase,
//It is highly discouraged to do so. Use PHP 8.
const PHP7_ENABLED = false;

// ReCAPTCHA settings
// set it to null for no recaptcha, else put on your site key & private key
const SITE_KEY = null;
const PRIVATE_KEY = null;

//Discord login
//be sure to add a slash to the end of the url
const WEBSITE_URL = "http://localhost/";
const CLIENT_ID = "";
const CLIENT_SECRET = "";

//Let it die (site closed)
const LET_IT_DIE = true;

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

//In this category you can setup custom emojis that can be sent in a message like this :emojiname: (like discord) (18x18)
const EMOJIS = ["oldverse"=>"/assets/img/favicon.ico"];
