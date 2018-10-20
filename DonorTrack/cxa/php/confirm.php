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
		<title><?=$GLOBALS["sitetitle"]?> - <?=(!empty($CONFOPTS["title"]) ? $CONFOPTS["title"] : "Confirm Action")?></title>
		<link rel="stylesheet" type="text/css" href="css/cxa-ui.css">
		<link rel="icon" type="image/png" href="./img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<div id="main" style="min-height: 275px;">
			<div id="topbar" class="loginbar noselect"><?php cxa_header() ?></div>
			<div id="login">
					<div id="loginerror">
						<?php if(empty($CONFOPTS["message"])){ ?>
							Are you sure?
						<?php }else{echo $CONFOPTS["message"];} ?>
					</div>
			</div>
			<div>
				<div class="bottombutton dual-left noselect" onclick="window.location.assign('<?=(!empty($CONFOPTS["posAction"]) ? $CONFOPTS["posAction"] : $_SERVER["PHP_SELF"]."?yes")?>')" >
					<?=(!empty($CONFOPTS["posChoice"]) ? $CONFOPTS["posChoice"] : "Yes")?>
				</div>
				<div class="bottombutton dual-right noselect" onclick="window.location.assign('<?=(!empty($CONFOPTS["negAction"]) ? $CONFOPTS["negAction"] : $_SERVER["PHP_SELF"]."?no")?>')" >
					<?=(!empty($CONFOPTS["negChoice"]) ? $CONFOPTS["negChoice"] : "No")?>
				</div>
			</div>
			<div id="footer" class="loginbar" ><?php cxa_footer() ?></div>
		</div>
	</body>
</html>