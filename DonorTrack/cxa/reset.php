<?php
include('php/guestsession.php');
include('meta.php');

function delete_by_id($id){
	global $conn;
	if($conn->query("DELETE FROM password_reset WHERE userid=".$id." LIMIT 1")){
		return true;
	}else{
		error_log($conn->error);
		return false;
	}
}

function verify_request(){
	if(tryField("token")){
		if(isset($_GET["token"])){
			$token=strtr($_GET["token"], '-_,', '+/=');
			$_GET["token"]=$token;
		}else{
			$token=$_POST["token"];
		}
		global $conn;
		$result=$conn->query("SELECT userid, expires FROM password_reset WHERE token=\"".hash('sha256', base64_decode($token))."\" LIMIT 1");
		if($result && $result->num_rows==1){
			$result = $result->fetch_assoc();
			if($result["expires"]>time()){
				return $result["userid"];
			}else{
				delete_by_id($result["userid"]);
				$regerr="This link has expired. Please contact your administrator to reset your password.";
				$regtitle="Error";
				include("php/reg-ok.php");
				return false;
			}
		}else{
			$regerr="Invalid token. Please contact your administrator to reset your password.";
			$regtitle="Error";
			include("php/reg-ok.php");
			return false;
		}
	}elseif(!empty($_SESSION["userid"])){
		return $_SESSION["userid"];
	}else{
		return false;
	}
}

function show_form($regerr=""){
?>
<html>
	<head>
		<title><?=$GLOBALS["sitetitle"]?> - Password Reset</title>
		<link rel="stylesheet" type="text/css" href="css/cxa-ui.css">
		<link rel="icon" type="image/png" href="./img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			h4{
				margin: 2px 0px;
			}
			p{
				margin: 2px 0px 8px 0px;
			}
		</style>
	</head>
	<body>
		<form action="reset.php" method="post" id="main">
			<div id="topbar" class="loginbar noselect"><?php cxa_header() ?></div>
			<div id="login" style="padding-bottom: 5px;">
				<?php if(tryField("token")){ ?>
				<input type="hidden" name="token" <?=tryFieldValue("token")?> />
				<h4>Password Reset</h4>
				<p>Please enter your current username and new password.</p>
				&nbsp;Username:<br/>
				<input type="text" name="username" class="registertext" <?=tryFieldValue("username")?>/><br/>
				<?php }else{ ?>
				<h4>Password Change: <?=$_SESSION["userdata"]["username"]?></h4>
				<p>Please enter your new password.</p>
				<?php } ?>
				&nbsp;New Password:<br/>
				<input type="password" name="password1" class="registertext"/><br/>
				&nbsp;Confirm Password:<br/>
				<input type="password" name="password2" class="registertext" style="margin-bottom:10px;"/><br/>
				<?php
					if(!empty($regerr)){
						echo "<div id=\"loginerror\">".$regerr."</div>";
					}
				?>
				<input type="submit" style="position: absolute; height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" tabindex="-1"/>
			</div>
			<div id="bottombar" class="loginbar noselect" onclick="document.getElementById('main').submit(); return false;">Reset Password&nbsp;&nbsp;</div>
			<div id="footer" class="loginbar" ><?php cxa_footer() ?></div>
		</form>
	</body>
</html>
<?php
}

if($_SERVER["REQUEST_METHOD"]=="POST" && ($userid=verify_request())!==false){
	$regerr="";
	if((empty($_POST["username"]) && tryField("token")) || empty($_POST["password1"]) || empty($_POST["password2"])){
		$regerr.="Please fill out all the fields.<br/>";
	}
	if((!empty($_POST["password1"]) && strlen($_POST["password1"])<8) ||(!empty($_POST["password2"]) && strlen($_POST["password2"])<8)){
		$regerr.="Your password must be 8 characters long.<br/>";
	}
	if(!empty($_POST["password1"]) && !empty($_POST["password2"]) && $_POST["password1"]!=$_POST["password2"]){
		$regerr.="Your passwords must match.<br/>";
	}
	if(!empty($_POST["username"]) && tryField("token")){
		if($result=$conn->query("SELECT username FROM users WHERE userid=$userid LIMIT 1")){
			if($result->num_rows==1){
				$result = $result->fetch_assoc();
				if($result["username"]!=$_POST["username"]){
					$regerr.="The username is incorrect.<br/>";
				}
			}else{
				delete_by_id($userid);
				error_log("Removed invalid password_reset record with nonexistant userid ".$userid);
				$regerr="Internal error. Please contact your administrator to reset your password.";
				$regtitle="Error";
				include("php/reg-ok.php");
				exit;
			}
		}else{
			$regerr.="Database error.<br/>";
		}
	}
	if($regerr==""){
		$password=password_hash($conn->escape_string($_POST['password1']),PASSWORD_BCRYPT);
		if($conn->query("UPDATE users SET password=\"$password\" WHERE userid=$userid LIMIT 1")){
			delete_by_id($userid);
			if(tryField("token")){
				$regmsg="Your password has been successfully reset.";
				$regtitle="Password Reset";
			}else{
				$regmsg="Your password has been successfully changed.";
				$regtitle="Password Changed";
			}
			include("php/reg-ok.php");
		}else{
			show_form("Database error.<br/>");
		}
	}else{
		show_form($regerr);
	}
}elseif(verify_request()!==false){
	show_form();
}else{
	$regerr="Please contact your administrator to reset your password.";
	$regtitle="Error";
	require_once("php/reg-ok.php");
}

$conn->close();
?>