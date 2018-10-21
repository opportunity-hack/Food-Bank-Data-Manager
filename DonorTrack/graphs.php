<?php
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/session.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/dashboard.php');

use CXA\DashboardInterface;

$dbi = new DashboardInterface($conn);
echo($dbi->get_latest_frame(1));
?>
<html>
	<head>
		<title><?=$sitetitle?> - Graphical Statistics</title>
		<link rel="stylesheet" type="text/css" href="/cxa/css/cxa-flex.css">
		<link rel="icon" type="image/png" href="/cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
	</head>
	<body>
		<div id="main" style="width: 500px; height: 500px;" >
			<div id="topbar" class="loginbar noselect"><?php cxa_header("Graphical Statistics") ?></div>
			<iframe id="content" src="<?=$dbi->get_latest_frame(1)?>"></iframe>
			<div id="footer"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>