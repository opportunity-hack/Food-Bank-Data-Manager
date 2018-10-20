<?php
/*
guestsession.php - Guest session management script for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

include("config.php");
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
if(!isset($_SESSION)){
	session_start();
}
if(!function_exists('hash_equals')) {
  function hash_equals($str1, $str2) {
    if(strlen($str1) != strlen($str2)) {
      return false;
	  error_log("BAD");
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
      return !$ret;
    }
  }
}
function newGuest(){
	global $conn;
	global $defaultcompetition;
	$result=$conn->query('SELECT id FROM competitions WHERE active="1" LIMIT 1');
	if($result && $result->num_rows==1){
		$newcomp=$result->fetch_assoc();
		$defaultcompetition=$newcomp['id'];
	}
	$_SESSION['userid']=777;
	$_SESSION['userdata']['username']="guest";
	$_SESSION['userdata']['name']="guest";
	$_SESSION['userdata']['authorization']=0;
	if(!empty($_COOKIE['guestcomp'])){
		$_SESSION['userdata']['competition']=$_COOKIE['guestcomp'];
	}else{
		$_SESSION['userdata']['competition']=$defaultcompetition;
	}
}
function end_session(){
	global $thedomain;
	unset($_SESSION);
	session_destroy();
	setcookie('remember','',1,'/',$thedomain,true,true);
	unset($_COOKIE['remember']);
	header('Location: cxa/login.php');
}
function generate_ptoken() {
	global $thedomain;
	$selector = base64_encode(openssl_random_pseudo_bytes(9));
	$authenticator = openssl_random_pseudo_bytes(33);
	
	setcookie(
		'remember',
		$selector.':'.base64_encode($authenticator),
		time() + 864000,
		'/',
		$thedomain,
		true, // TLS-only
		true  // http-only
	);
	$token = hash('sha256', $authenticator);
	$expires = time() + 864000;
	$userid = $_SESSION['userid'];
	global $conn;
	$conn->query("INSERT INTO auth_tokens (selector, token, userid, expires) VALUES (\"$selector\", \"$token\", $userid, $expires)");
}
function authorized($level){
	if(!empty($_SESSION["userdata"]) && !empty($_SESSION["userdata"]["authorization"])){
		if($_SESSION["userdata"]["authorization"]>=$level){
			return true;
		}else{
			error_log('Failed access attempt at level '.$level.' by '.$_SESSION["userdata"]["username"].' with authorization level '.$_SESSION["userdata"]["authorization"].'!');
			return false;
		}
	}else{
		error_log('Failed access attempt at level by guest!');
		return false;
	}
}
function boot_user($level){
	if(!authorized($level)){
		$loginerror="You are unauthorized to view that page.";
		error_log('Failed access attempt at level '.$level.' by '.$_SESSION["userdata"]["username"].' with authorization level '.$_SESSION["userdata"]["authorization"].'!');
		header('Location: cxa/login.php');
	}
}
function tryField($field){
	if(!empty($_GET[$field])){
		return $_GET[$field];
	}elseif(!empty($_POST[$field])){
		return $_POST[$field];
	}else{
		return "";
	}
}
function tryFieldValue($field){
	$value=tryField($field);
	if($value != ""){
		return "value=\"$value\" ";
	}
	return "";
}
if (empty($_SESSION['userid']) && !empty($_COOKIE['remember'])) {
    list($selector, $authenticator) = explode(':', $_COOKIE['remember']);
    $result = $conn->query("SELECT * FROM auth_tokens WHERE selector = \"$selector\"");
	if($result){
		$row = $result->fetch_assoc();
		$hash = hash('sha256', base64_decode($authenticator));
		if (hash_equals($row['token'], $hash) && $row["expires"]>time()) {
			$_SESSION['userid'] = $row['userid'];
			$uid = $_SESSION['userid'];
			$sql = "SELECT * FROM users WHERE userid = '$uid'";
			$result = $conn->query($sql);
			if ($result->num_rows == 1) {
				$row=$result->fetch_assoc();
				$_SESSION["userdata"]=$row;
				generate_ptoken();
			}else{newGuest();}
		}else{newGuest();}
	}else{newGuest();}
}
elseif(empty($_SESSION['userid'])){
	newGuest();
}
?>