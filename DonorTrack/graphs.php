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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/cxa/js/jquery.min.js"><\/script>')</script>
		<script>
			window.current_graph = 1;
			window.graph_count = 5;
			
			function next_graph ()
			{
				window.current_graph += 1;
				if (window.current_graph > window.graph_count)
				{
					window.current_graph = window.graph_count;
					return;
				}
				
				var graphs = document.getElementById('content').children;
				graphs[window.current_graph - 2].style = "display: none;";
				graphs[window.current_graph - 1].style = "display: block;";
				
				update_status();
			}
			
			function prev_graph ()
			{
				window.current_graph -= 1;
				if (window.current_graph < 1)
				{
					window.current_graph = 1;
					return;
				}
				
				var graphs = document.getElementById('content').children;
				graphs[window.current_graph - 0].style = "display: none;";
				graphs[window.current_graph - 1].style = "display: block;";
				
				update_status();
			}
			
			function update_status ()
			{
				var status = document.getElementById('graphstatus');
				status.textContent = "Graph " + window.current_graph + "/" + window.graph_count;
			}
		</script>
	</head>
	<body>
		<div id="fill-main">
			<div id="topbar" class="loginbar noselect"><?php cxa_header("Graphical Statistics") ?>
				<div id="nextbtn" class="hdrbtn noselect" onclick="next_graph();" style="margin-right: 10px;">Next</div>
				<div id="graphstatus" class="hdrin noselect">Graph 1/5</div>
				<div id="prevbtn" class="hdrbtn noselect" onclick="prev_graph();">Previous</div>
			</div>
			<div id="content">
				<iframe class="frame-fill" style="display: block;" src="<?=$dbi->get_latest_frame(1)?>"></iframe>
				<iframe class="frame-fill" style="display: none;" src="<?=$dbi->get_latest_frame(2)?>"></iframe>
				<iframe class="frame-fill" style="display: none;" src="<?=$dbi->get_latest_frame(3)?>"></iframe>
				<iframe class="frame-fill" style="display: none;" src="<?=$dbi->get_latest_frame(4)?>"></iframe>
				<iframe class="frame-fill" style="display: none;" src="<?=$dbi->get_latest_frame(5)?>"></iframe>
			</div>
			<div id="footer"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>