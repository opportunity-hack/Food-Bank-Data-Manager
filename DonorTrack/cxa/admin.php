<!--
landing.php - Example page for CXA Auth LW web data framework demo.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<?php
include('php/session.php');
include('meta.php');
if(!authorized(4)){
	header("Location: /index.php");
}
?>
<html>
	<head>
		<title><?=$sitetitle?> - Admin Menu</title>
		<link rel="stylesheet" type="text/css" href="css/cxa-ui.css">
		<link rel="icon" type="image/png" href="./img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/jquery.min.js"><\/script>')</script>
		<script src="js/cxa-ui.js"></script>
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
				<?php
				if(authorized(4)){echo '
				<div class="action drawer-handle" id="dh-admin">
					Administration
				</div>
				<div class="drawer" id="d-admin">
					<a class="action stored" href="./approveusers.php">
						Approve User Requests
					</a>
					<a class="action stored" href="./users.php">
						Manage Users
					</a>
					<a class="action stored" href="./register.php">
						New User
					</a>
				</div>
				';}
				?>
				<a class="action" href="./logout.php">
					Logout
					<?php echo $_SESSION['userdata']['username']; ?>
				</a>
			</div>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>
<?php
$conn->close();
?>