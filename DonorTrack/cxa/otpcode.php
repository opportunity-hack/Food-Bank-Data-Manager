<?php
require_once('php/ga.php');
require_once('php/guestsession.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(!empty($_SESSION['regsql']) && !empty($_SESSION['otpsecret']) && !empty($_POST["otp"])){ //Onboarding
		if(Google2FA::verify_key($_SESSION["otpsecret"],$_POST["otp"])){
			unset($_SESSION["otpsecret"]);
			unset($_SESSION["otpuri"]);
			if($conn->query($_SESSION["regsql"])){
				unset($_SESSION["regsql"]);
				include('php/reg-ok.php');
			}else{
				$registererror="Database error!";
				unset($_SESSION["regsql"]);
				include('php/reg.php');
			}
		}else{
			$registererror="Incorrect OTP!";
			include('php/reg-img.php');
		}
	}elseif(!empty($_SESSION["userdata"]) && !empty($_SESSION['otpsecret']) && !empty($_POST["otp"])){ //Set up 2FA for existing user
		if(Google2FA::verify_key($_SESSION["otpsecret"],$_POST["otp"])){
			if($conn->query('UPDATE users SET otpsecret="'.$_SESSION["otpsecret"].'" WHERE userid="'.$_SESSION["userdata"]["userid"].'"')){
				$_SESSION["userdata"]["otpsecret"]=$_SESSION["otpsecret"];
				unset($_SESSION["otpsecret"]);
				unset($_SESSION["otpuri"]);
				unset($_SESSION["regsql"]);
				$regmsg="Two-Factor Authentication (OTP) successfully reset.";
				$regtitle="2FA Reset";
				include('php/reg-ok.php');
			}else{
				$registererror="Database error!";
				include('php/reg-img.php');
			}
		}else{
			$registererror="Incorrect OTP!";
			include('php/reg-img.php');
		}
	}elseif(isset($_POST["otp"])){
		$registererror="Enter your OTP.";
		include('php/reg-img.php');
	}
}elseif(!empty($_SESSION['regsql']) && !empty($_SESSION['otpuri'])){
	include('php/reg-img.php');
}elseif(!empty($_SESSION['userdata'])){
	if(isset($_GET["reset"])){
		$CONFOPTS=[
			"title" => "Reset 2FA",
			"message" => "Are you sure you want to reset Two-Factor Authentication?<br/>This will cause any OTP generators you have connected to stop working.",
			"posAction" => $_SERVER["PHP_SELF"]."?reset-confirmed",
			"negAction" => "/index.php",
		];
		if(empty($_SESSION["userdata"]["otpsecret"])){
			$CONFOPTS["message"]="Are you sure you want to set up Two-Factor Authentication (2FA)?<br/>A one-time password (OTP) will be required at every login.";
		}
		include('php/confirm.php');
	}elseif(isset($_GET["reset-confirmed"])){
		$_SESSION['otpsecret']=Google2FA::generate_secret_key();
		$_SESSION['otpuri']='otpauth://totp/MCDM:'.$_SESSION["userdata"]["username"].'@CXA?secret='.$_SESSION["otpsecret"];
		include('php/reg-img.php');
	}elseif(isset($_GET["remove"])){
		$CONFOPTS=[
			"title" => "Remove 2FA",
			"message" => "Are you sure you want to remove Two-Factor Authentication (your OTP)?",
			"posAction" => $_SERVER["PHP_SELF"]."?remove-confirmed",
			"negAction" => "/index.php",
		];
		include('php/confirm.php');
	}elseif(isset($_GET["remove-confirmed"])){
		if($conn->query('UPDATE users SET otpsecret="" WHERE userid="'.$_SESSION["userdata"]["userid"].'"')){
			$_SESSION["userdata"]["otpsecret"]="";
			$regmsg="Two-Factor Authentication (OTP) successfully removed.";
			$regtitle="2FA Removed";
			include('php/reg-ok.php');
		}else{
			$regerr="Database error!";
			$regtitle="Error";
			include('php/reg-ok.php');
		}
	}else{
		exit();
	}
}else{
	include('php/reg.php');
}
?>