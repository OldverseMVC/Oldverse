<?php
// HACK: fix for including config in multiple dirs down
// Please fix this!
$config_double = 0;

if($config_double == 1)
{
	require_once('../../config.php');
}
else
{
	require_once('../config.php');
}
if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    exit;
}

if(in_array($_SERVER['REMOTE_ADDR'], FORBIDDEN_IPS) && $_SERVER['REQUEST_URI'] !== '/ip-banned'){
    header("Location: /ip-banned");
    exit;
}

// Connect to DB
$db = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); // DB_NAME

// Exit if no db connection
if (!$db)
{
    http_response_code(500);
    header("Content-Type: text/html");
	exit(require_once "500.php");
}

// Set Timezone
date_default_timezone_set(TIMEZONE);

// Set Timezone in DB
$db->query('SET time_zone = "' . $db->real_escape_string(TIMEZONE) . '"');

//Actual user code
session_name("oldv_session");
session_start();


//Check if token exist, if no then redirect to logout page.
if(!isset($skip_t_check) && isset($_SESSION['token'])){
    $stmt = $db->prepare("SELECT user FROM tokens WHERE token = ?");
    $stmt->bind_param('s', $_SESSION['token']);
    $stmt->execute();
    if($stmt->error){
        exit("Fatal error! We're sorry for the disruption.");
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        header("Location: /account/logout?location=/");
    }
}

if($_SERVER['REQUEST_URI']!=='/banned'){
    $user = getUserID($_SESSION['token']);
    $stmt = $db->prepare("SELECT reason FROM ban WHERE target = ?");
    $stmt->bind_param('i', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows>0){
        header("Location: /banned");
        exit;
    }
}

function getUser($token){
    global $db;
    $stmt = $db->prepare("SELECT user FROM tokens WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    if($stmt->error){
        return false;
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        return 'no_user';
    }
    $row = $result->fetch_array();
    $stmt = $db->prepare("SELECT id, username, mii_hash, level FROM users WHERE id = ?");
    $stmt->bind_param('i', $row['user']);
    $stmt->execute();
    if($stmt->error){
        return false;
    }
    $result = $stmt->get_result();
    return $result->fetch_array();
}
function getAvatar($hash, $feeling){
    switch($feeling){
        case 0:
            return "https://mii-secure.cdn.nintendo.net/".$hash."_normal_face.png";
        case 1:
            return "https://mii-secure.cdn.nintendo.net/".$hash."_happy_face.png";
        case 2:
            return "https://mii-secure.cdn.nintendo.net/".$hash."_like_face.png";
        case 3:
            return "https://mii-secure.cdn.nintendo.net/".$hash."_surprised_face.png";
        case 4:
            return "https://mii-secure.cdn.nintendo.net/".$hash."_frustrated_face.png";
        case 5:
            return "https://mii-secure.cdn.nintendo.net/".$hash."_puzzled_face.png";
    }
}
function getYeahText($feeling, $is_yeah){
    switch($feeling){
        case 0:
            return $is_yeah == "empathy-added" ? 'Unyeah' : 'Yeah!';
        case 1:
            return $is_yeah == "empathy-added" ? 'Unyeah' : 'Yeah!';
        case 2:
            return $is_yeah == "empathy-added" ? 'Unyeah' : 'Yeah❤';
        case 3:
            return $is_yeah == "empathy-added" ? 'Unyeah' : 'Yeah?!';
        case 4:
            return $is_yeah == "empathy-added" ? 'Unyeah' : 'Yeah...';
        case 5:
            return $is_yeah == "empathy-added" ? 'Unyeah' : 'Yeah...';
        default:
            return 'olv.yeah.yeahtext';
    }
}
function getFeeling($feeling){
    switch($feeling){
        case 0:
            return 'normal';
        case 1:
            return 'happy';
        case 2:
            return 'like';
        case 3:
            return 'surprised';
        case 4:
            return 'frustrated';
        case 5:
            return 'puzzled';
        default:
            return 'olv.yeah.yeahtext';
    }
}
function selected($page, $username=null){
    switch($page){
        case 'communities':
            if($_SERVER['REQUEST_URI']=='/'){
                return 'selected';
            }else{
                return '';
            }
            break;
        case 'my-menu':
            if($_SERVER['REQUEST_URI']=='/my-menu'){
                return 'selected';
            }else{
                return '';
            }
            break;
        case 'activity':
            if($_SERVER['REQUEST_URI']=='/activity'){
                return 'selected';
            }else{
                return '';
            }
            break;
        case 'notifications':
            if($_SERVER['REQUEST_URI']=='/news/my_news'){
                return 'selected';
            }else{
                return '';
            }
            break;
        case 'my-userpage':
            return $_SERVER['REQUEST_URI']=='/users/'.$username ? 'selected' : '';
    }
}
function getCommunityType($type, $if_text=true){
    switch($type){
        case 0:
            return $if_text ?  '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu.png"></span><span class="text">Wii U Games</span>': '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu.png"></span>';
        case 1:
            return $if_text ?  '<span class="platform-tag"><img src="/assets/img/platform-tag-3ds.png"></span><span class="text">3DS Games</span>': '<span class="platform-tag"><img src="/assets/img/platform-tag-3ds.png"></span>';
        case 2:
            return $if_text ?  '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu-3ds.png"></span><span class="text">Wii U/3DS Games</span>': '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu-3ds.png"></span>';
    }
}
function requireAuth(){
    if(empty($_SESSION['token'])){
        require_once "header.php";?>
        <div class="warning-content warning-content-forward">
            <div>
                <strong>Welcome to <?php print(SITE_NAME); ?>!</strong>
                <p>You must sign in to view this page.</p>
                <a class="button" href="/account/login">Sign In</a>
            </div>
        </div> <?php
        exit;
    }
}
function showError($http_response_code, $error){
    require_once "header.php";?>
    <div class="warning-content warning-content-forward">
        <div>
            <p><?= $error ?></p>
        </div>
    </div> <?php
    http_response_code($http_response_code);
    exit;
}
function showNoContent($text)
{
    echo '<div class="no-content"><p>' . $text . '</p></div>';
}
function showJSONError($responseCode, $errorCode, $message)
{
    http_response_code($responseCode);
    header('Content-Type: application/json');
    exit(json_encode(['success' => 0, 'errors' => [['error_code' => $errorCode, 'message' => $message]]]));
}
function getTimestamp($date){
    //i still don't understand why is this not already implemented inside MySQL
    global $db;
    $stmt = $db->prepare("SELECT UNIX_TIMESTAMP(:date)");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    if($stmt->error){
        return null;
    }
    $result = $stmt->get_result();
    $row = $stmt->fetch_array();
    return $row[0];
}
function getTimeAgo( $time )
    {
        $time_difference = time() - $time;
    
        if( $time_difference < 1 ) { return 'less than 1 second ago'; }
        $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                    30 * 24 * 60 * 60       =>  'month',
                    24 * 60 * 60            =>  'day',
                    60 * 60                 =>  'hour',
                    60                      =>  'minute',
                    1                       =>  'second'
        );
    
        foreach( $condition as $secs => $str )
        {
            $d = $time_difference / $secs;
    
            if( $d >= 1 )
            {
                $t = round( $d );
                return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }
function checkIfYeah($userid, $postid, $is_reply = false){
    global $db;
    $type = $is_reply ? 1 : 0;
    $stmt = $db->prepare("SELECT * FROM empathies WHERE `source`=? AND `target`= ? AND type= ? ");
    $stmt->bind_param('iii', $userid, $postid, $type);
    $stmt->execute();
    if($stmt->error){
        showError(500, 5255545, "An error occured while checking for existing yeahs.");
    }
    $result = $stmt->get_result();
    if($result->num_rows != 0){
        return 'empathy-added';
    }else{
        return '';
    }
}
function getAllYeahs($postid, $is_reply = false){
    global $db;
    $type = $is_reply ? 1 : 0;
    $stmt = $db->prepare("SELECT source FROM empathies WHERE `target`= ? AND type= ? ");
    $stmt->bind_param('ii', $postid, $type);
    $stmt->execute();
    if($stmt->error){
        showError(500, 5255545, "An error occured while grabbing yeahs.");
    }
    $result = $stmt->get_result();
    return $result;
}
function getUserID($token){
    global $db;
    $stmt = $db->prepare("SELECT user FROM tokens WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    if($stmt->error){
        return false;
    }
    $result = $stmt->get_result();
    if($result->num_rows==0){
        return 'no_user';
    }
    $row = $result->fetch_array();
    return $row['user'];
}
function sendNotif($source, $target, $type, $url, $additional_id=null){
    global $db;
    $stmt = $db->prepare("INSERT INTO news(source, target, type, url, additional_id) VALUES(?, ?, ?, ?, ?)");
    $stmt->bind_param('iiisi', $source, $target, $type, $url, $additional_id);
    $stmt->execute();
    if($stmt->error){
        return false;
    }
    return true;
}