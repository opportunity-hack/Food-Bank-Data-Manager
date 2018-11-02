<?php
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/util.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');

use CXA\Util;

if (isset($_POST["action"]))
{
	switch($_POST["action"])
	{
		case "graphregen":
			chdir("../FBM Utility/");
			if (empty(trim(shell_exec("$pypath \"GenerateGraphs.py\" 2>&1"))))
			{
				$regmsg = "Graphs Regenerated Successfully!";
				$regtitle = "Regenerate Graphs";
				include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/reg-ok.php');
				exit();
			}
			else
			{
				$interError = true;
			}
			break;
	}
	
}
?>
<html>
	<head>
		<title>Report Administration - MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-flex.css">
		<link rel="icon" type="image/png" href="cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
		<script src="https://unpkg.com/flatpickr"></script>
		<script>
			function special_submit ()
			{
				var element = document.getElementById('loginerror');
				if (element !== null) {
					element.parentNode.removeChild(element);
				}
				
				document.getElementById('login').submit();
				return false;
			}
		</script>
	</head>
	<body>
		<div id="main" style="min-height: 375px;">
			<div id="topbar" class="loginbar noselect">
				<?php cxa_header() ?>
			</div>
			<div class="welcomebar">
				Report Administration<br/>
			</div>
			<form action="reporttasks.php" method="post" id="login" style="">
				<?php
				if (isset($interError))
				{
				?>
					<div id="loginerror">
						Internal error!
					</div>
				<?php
				}
				?>
				<button type="submit" class="loginbutton" name="action" value="graphregen">
					Regenerate Graphs
				</button>
				<p class="flabel" style="margin-top: 16px;">These tasks may take a while. Please be patient.</span></p>
			</form>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
		<script>
			flatpickr("#date", {dateFormat: 'Y-m'});
		</script>
	</body>
</html>