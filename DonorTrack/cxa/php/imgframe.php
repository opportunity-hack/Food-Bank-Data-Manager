<?php
require_once('meta.php');
?>
<html>
	<head>
		<title><?=$GLOBALS["sitetitle"]?> - <?=$imgtitle?></title>
		<link rel="stylesheet" type="text/css" href="css/cxa-ui.css">
		<link rel="icon" type="image/png" href="./img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<form action="otpcode.php" method="post" id="main" style="min-height: 275px;">
			<div id="topbar" class="loginbar noselect"><?php cxa_header() ?></div>
			<div id="login" style="height: auto; margin-bottom: 40;">
					<div id="loginerror" style="background: #2a2; color: #eee; width: 180px; margin: 0 auto; padding: 4px;"><img src="<?=$imgsrc?>" /></div>
			</div>
			<div id="bottombar" class="loginbar noselect" onclick="window.location.assign('/index.php')">Back to Home&nbsp;&nbsp;</div>
			<div id="footer" class="loginbar" ><?php cxa_footer() ?></div>
		</form>
	</body>
</html>