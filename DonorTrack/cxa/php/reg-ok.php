<!--
reg-ok.php - Registration confirmation page for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<?php
require_once('meta.php');
?>
<html>
	<head>
		<title><?=$GLOBALS["sitetitle"]?> - <?=(!empty($regtitle) ? $regtitle : "Registration Successful")?></title>
		<link rel="stylesheet" type="text/css" href="css/cxa-ui.css">
		<link rel="icon" type="image/png" href="./img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<form action="register.php" method="post" id="main">
			<div id="topbar" class="loginbar noselect"><?php cxa_header() ?></div>
			<div id="login">
					<?php if(!empty($regmsg) || empty($regerr)){ ?>
					<div id="loginerror" style="background: #2a2; color: #eee">
						<?php if(empty($regmsg)){ ?>
							Your request has been recieved and should be reviewed within 24 hours.
						<?php }else{echo $regmsg;} ?>
					</div>
					<?php } if(!empty($regerr)){ ?>
					<div id="loginerror">
						<?= $regerr ?>
					</div>
					<?php } ?>
			</div>
			<div id="bottombar" class="loginbar noselect" onclick="window.location.assign('/index.php')">Back to Home&nbsp;&nbsp;</div>
			<div id="footer" class="loginbar" ><?php cxa_footer() ?></div>
		</form>
	</body>
</html>