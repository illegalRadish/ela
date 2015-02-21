
<html>
<head>
<style>
#goback {
background-color: black;
padding: 15px;
position: fixed;
z-index : 1;
}
</style>
</head>
<body>
<div id="goback">
<center>
<FORM METHOD="LINK" ACTION="index.html">
<INPUT TYPE="submit" VALUE="Go Back">
</FORM>
</center>
</div>
<BR>

<?php
session_start();

$ipaddress = $_SERVER["REMOTE_ADDR"];
date_default_timezone_set('Europe/London');
$date = date('m/d/Y h:i:s a', time());

    function get_web_page( $url )
    {
        $user_agent='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.111 Safari/537.36';

        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }
	

$msg='';
if($_SERVER["REQUEST_METHOD"] == "POST")
{
$recaptcha=$_POST['g-recaptcha-response'];
if(!empty($recaptcha))
{
include("getCurlData.php");
$google_url="https://www.google.com/recaptcha/api/siteverify";
$secret='6LepRwITAAAAAElgu44jDcz4l3T_sVhD8AeZ7sV_';
$ip=$_SERVER['REMOTE_ADDR'];
$url=$google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
$res=getCurlData($url);
$res= json_decode($res, true);
//reCaptcha success check 
if($res['success'])
{
$URL = "http://".$_POST["URL"];

//Read a web page and check for errors:

$result = get_web_page($URL);
error_log("\r\n<IP> " . $ipaddress . " <URL> " . $URL . " <DATE> ". $date . " <CAPTCHA> TRUE", 3, "/var/www/projects/URLUnblocker/logs/main.log");

if ( $result['errno'] != 0 )
    echo ('Error: Incorrect URL or timed out');

if ( $result['http_code'] != 200 )
    echo ('Error: Invalid Page or no Permission');

$page = $result['content'];

echo ($page);

}
else
{
$msg="Please re-enter your reCAPTCHA.";
error_log("\r\n<IP> " . $ipaddress . " <URL> " . $URL . " <DATE> ". $date . " <CAPTCHA> FALSE", 3, "/var/www/projects/URLUnblocker/logs/main.log");
}

}
else
{
$msg="Please re-enter your reCAPTCHA.";
error_log("\r\nURL: " . $URL . " IP: " . $ipaddress . " DATE: ". $date . " CAPTCHA: FALSE", 3, "/var/www/projects/URLUnblocker/logs/main.log");
}

}

echo $msg;
?>
</body>
</html>