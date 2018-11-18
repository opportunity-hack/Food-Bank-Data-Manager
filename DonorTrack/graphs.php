<?php
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/guestsession.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/dashboard.php');

use CXA\DashboardInterface;

$dbi = new DashboardInterface($conn);
?>
<html>
	<head>
		<title><?=$sitetitle?> - Graphical Statistics</title>
		<link rel="stylesheet" type="text/css" href="/cxa/css/cxa-flex.css" />
		<link rel="icon" type="image/png" href="/cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
		<style>
		.row-container {
			display: flex;
			flex-direction: row;
			flex-grow: 1;
		}
		.row-item {
			display: block;
			flex: 1;
		}
		iframe {
			border: 1px solid #aaa;
			box-sizing: border-box;
			height: 100%;
			width: 100%;
		}
		</style>
	</head>
	<body>
		<div id="fill-main">
			<div id="topbar" class="loginbar noselect"><?php cxa_header("Graphical Statistics") ?>
			</div>
			<div class="row-container">
				<div class="row-item"><iframe src="<?=$dbi->get_latest_frame(1)?>"></iframe></div>
				<div class="row-item"><iframe src="<?=$dbi->get_latest_frame(2)?>"></iframe></div>
			</div>
			<div class="row-container">
				<div class="row-item"><iframe src="<?=$dbi->get_latest_frame(3)?>"></iframe></div>
				<div class="row-item"><iframe src="<?=$dbi->get_latest_frame(4)?>"></iframe></div>
				<div class="row-item"><iframe src="<?=$dbi->get_latest_frame(5)?>"></iframe></div>
			</div>
			<div id="footer"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>