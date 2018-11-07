<!--
table_template.php - Frontend template for table management system
Copyright (c) 2018 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
-->

<html>
	<head>
		<title><?=$sitetitle?> - <?=$table["data"]["title"]?></title>
		<link rel="stylesheet" type="text/css" href="/cxa/css/cxa-ui.css">
		<link rel="stylesheet" type="text/css" href="/cxa/css/cxa-um.css">
		<link rel="icon" type="image/png" href="/cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=0.5">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/cxa/js/jquery.min.js"><\/script>')</script>
		<script src="/cxa/js/cxa-ui.js"></script>
		<script src="/cxa/js/cxa-table.js"></script>
		<script>
		var specification = <?=json_encode($table)?>;
		
		$(document).ready(
			function ()
			{
				var table = new Table($('#split-c'), specification);
				$('#refresher').click(function(){table.refresh();});
			}
		);
		$(document).ready(CXAUI);
		</script>
	</head>
	<body>
		<div id="bigmain">
			<div id="topbar" class="loginbar noselect"><?php cxa_header($table["data"]["title"]) ?><div id="refresher" style="margin-left: 10px;"></div></div>
			<div id="split-c"></div>
			<div id="footer"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>