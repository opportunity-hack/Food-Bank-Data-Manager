<!--
index.php - Landing page for MCDM DonorTrack.
Copyright (c) 2016 James Rowley

This file is part of MCDM DonorTrack, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with MCDM DonorTrack.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<?php
session_start();
$_SESSION["return"]="/index.php";
include('cxa/php/session.php');
include('cxa/meta.php');
if(isset($_SESSION["userdata"])){
?>
<html>
	<head>
		<title>MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-ui.css">
		<link rel="icon" type="image/png" href="cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="cxa/js/jquery.min.js"><\/script>')</script>
		<script src="cxa/js/cxa-ui.js"></script>
		<script>
			$.get("/donorinter.php");
		</script>
	</head>
	<body>
		<div id="main">
			<div id="topbar" class="loginbar noselect"><?php cxa_header() ?></div>
			<?php
				if(isset($_SESSION['welcomed'])){
					echo '<div id="welcomebar" class="welcomebar" style="display:none">';
				}else{
					echo '<div id="welcomebar" class="welcomebar">';
					echo "<i>Welcome,</i> ".explode(' ',trim($_SESSION['userdata']['name']))[0].".<br/>";
					$_SESSION['welcomed']="yes";
				}
				echo '</div>';
			?>
			<div id="landing">
				<a class="action" href="./graphs.php">
					Graphical Statistics
				</a>
				<?php
				if(authorized(2)){echo '
				<a class="action" href="./searchdonors.php">
					Input Donation
				</a>
				<a class="action" href="./adddonor.php">
					Add New Donor
				</a>
				';}
				if(authorized(3)){echo '
				<div class="action drawer-handle" id="dh-reports">
					Reports
				</div>
				<div class="drawer" id="d-reports">
					<a class="action stored" href="./myreport.php">
						Monthy/Yearly Report
					</a>
					<a class="action stored" href="./foodreport.php">
						Food Intake Report
					</a>
					<a class="action stored" href="./guestreport.php">
						Guest/Outreach Report
					</a>
				</div>
				';}
				if(authorized(4)){echo '
				<div class="action drawer-handle" id="dh-admin">
					Administration
				</div>
				<div class="drawer" id="d-admin">
					<a class="action stored" href="./cxa/approveusers.php">
						Approve User Requests
					</a>
					<a class="action stored" href="./cxa/users.php">
						Manage Users
					</a>
					<a class="action stored" href="./cxa/register.php">
						New User
					</a>
				</div>
				';}elseif(authorized(3)){echo '
				<div class="action drawer-handle" id="dh-admin">
					Administration
				</div>
				<div class="drawer" id="d-admin">
					<a class="action stored" href="./cxa/approveusers.php">
						Approve User Requests
					</a>
					<a class="action stored" href="./cxa/register.php">
						New User
					</a>
				</div>
				';}
				?>
				<div class="action drawer-handle" id="dh-account">
					Account
				</div>
				<div class="drawer" id="d-account">
					<?php if($_SESSION['userdata']['otpsecret'] != ""){ ?>
					<a class="action stored" href="./cxa/otpcode.php?recall">
						Recall OTP
					</a>
					<a class="action stored" href="./cxa/otpcode.php?reset">
						Reset OTP
					</a>
					<a class="action stored" href="./cxa/otpcode.php?remove">
						Remove OTP
					</a>
					<?php }else{ ?>
					<a class="action stored" href="./cxa/otpcode.php?reset">
						Enable OTP
					</a>
					<?php } ?>
					<a class="action stored" href="./cxa/reset.php">
						Change Password
					</a>
					<a class="action stored" href="./cxa/logout.php">
						Logout
						<?php echo $_SESSION['userdata']['username']; ?>
					</a>
				</div>
			</div>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>
<?php
}
$conn->close();
?>