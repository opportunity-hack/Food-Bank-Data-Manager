<?php
/*
session.php - Authorization and session management system for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

include("config.php");
include("dbconn.php");

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

function end_session(){
	if(strpos($_SERVER['PHP_SELF'],'setdata.php')===false){
		global $thedomain;
		$_SESSION = array();
		unset($_SESSION);
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy();
		setcookie('remember','',1,'/',$thedomain,true,true);
		unset($_COOKIE['remember']);
		header('Location: /');
	}
}
function authorized($level){
	if(!empty($_SESSION["userdata"])){
		if($_SESSION["userdata"]["authorization"]>=$level){
			return true;
		}else{
			return false;
			error_log('Failed access attempt at level '.$level.' by '.$_SESSION["userdata"]["username"].' with authorization level '.$_SESSION["userdata"]["authorization"].'!');
		}
	}else{
		return false;
		error_log('Failed access attempt at level by guest!');
	}
}
function boot_user($level){
	if(!authorized($level)){
		$loginerror="You are unauthorized to view that page.";
		header('Location: /cxa/login.php');
		error_log('Failed access attempt at level '.$level.' by '.$_SESSION["userdata"]["username"].' with authorization level '.$_SESSION["userdata"]["authorization"].'!');
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
			}else{end_session();}
		}else{end_session();}
	}else{end_session();}
}elseif(empty($_SESSION['userid']) && strpos($_SERVER['PHP_SELF'],'login.php')===false){
	header('Location: /cxa/login.php');
}

if(!empty($_SESSION['userid']) && $_SESSION['userid']==777){
	end_session();
}
?>