<!--
login.php - Login script for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<?php
include('php/ga.php');
include('php/session.php');

$MAX_REPEATED_LOGINS = 5;

if(!isset($_SESSION["logintries"])){
	$_SESSION["logintries"]=0;
}

$return = "admin.php";
if(!empty($_SESSION["return"])){
	$return = $_SESSION["return"];
}

if(!empty($_SESSION["userid"])){
	header('Location: '.$return);
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if( !empty($_POST["username"]) && !empty($_POST["password"]) ){
		if($_SESSION["logintries"]>=$MAX_REPEATED_LOGINS){
			if(!isset($_SESSION["loginretry"])){
				$_SESSION["loginretry"]=time()+20;
				$loginerror="Maximum tries exceeded.<br/>Please wait 20 seconds.";
				include("php/log.php");
			}elseif($_SESSION["loginretry"]<time()){
				unset($_SESSION["loginretry"]);
				$_SESSION["logintries"]=0;
				include("php/log.php");
			}else{
				$loginwait=$_SESSION["loginretry"]-time();
				$loginerror="Maximum tries exceeded.<br/>Please wait $loginwait seconds.";
				include("php/log.php");
			}
		}else{
			$uname=$conn->escape_string($_POST['username']);
			$sql = "SELECT * FROM users WHERE username = '$uname'";
			$result = $conn->query($sql);
			if (is_object($result) && $result->num_rows == 1) {
				$row=$result->fetch_assoc();
				if(password_verify($_POST["password"],$row["password"])){
					if( $row["otpsecret"] == "" || ( !empty($_POST["otp"]) && Google2FA::verify_key($row["otpsecret"],$_POST["otp"]) ) ){
						$_SESSION["userdata"]=$row;
						$_SESSION["userid"]=$row["userid"];
						if(!empty($_POST["rememberme"])){
							generate_ptoken();
						}
						header('Location: '.$return);
					}else{
						$loginerror="Invalid one-time password.";
						$_SESSION["logintries"]+=1;
						include('php/log.php');
					}
				}else{
					$loginerror="Invalid username or password.";
					$_SESSION["logintries"]+=1;
					include('php/log.php');
				}
			}else{
				$loginerror="Invalid username or password.";
				$_SESSION["logintries"]+=1;
				include('php/log.php');
			}
		}
	}else{
		$loginerror="Please enter your login.";
		include('php/log.php');
	}
}else{
	include('php/log.php');
}
$conn->close();
?>