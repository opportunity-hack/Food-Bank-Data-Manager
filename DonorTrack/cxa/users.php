<!--
users.php - User management console for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<?php
include('php/session.php');
include('meta.php');
boot_user(4);
?>
<html>
	<head>
		<title><?=$sitetitle?> - User Management</title>
		<link rel="stylesheet" type="text/css" href="css/cxa-ui.css">
		<link rel="stylesheet" type="text/css" href="css/cxa-um.css">
		<link rel="icon" type="image/png" href="./img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/jquery.min.js"><\/script>')</script>
		<script src="js/cxa-ui.js"></script>
		<script src="js/cxa-um.js"></script>
		<script>$(cxaManageUsers);</script>
	</head>
	<body>
		<div id="bigmain">
			<div id="topbar" class="loginbar noselect"><?php cxa_header("User Manager") ?><div id="refresher" style="margin-left: 10px;"></div></div>
			<div id="split-c">
				<div class="theader" >
					<table class="teamtable alpha">
						<tr>
							<td class="col-pre">ID</td>
							<td class="col-18">Name</td>
							<td class="col-18">Username</td>
							<td class="col-18">E-Mail</td>
							<td class="col-18">New Password</td>
							<td class="col-18">TOTP Secret</td>
							<td class="col-7 hastip" tip="Authorization Level">Auth</td>
							<td class="col-post final-column"></td>
						</tr>
					</table>
				</div>
				<div id="tmain">
					<table id="userboard" class="teamtable">
					</table>
				</div>
			</div>
			<div id="footer"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>