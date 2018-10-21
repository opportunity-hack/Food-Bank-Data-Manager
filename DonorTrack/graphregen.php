<?php
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/util.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');

use CXA\Util;

if (isset($_POST["go"]))
{
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
}
?>
<html>
	<head>
		<title>Regenerate Graphs - MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-ui.css">
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
				Regenerate Graphs<br/>
			</div>
			<form action="graphregen.php" method="post" id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 40px;">
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
				<input type="hidden" name="go" value="go" />
				<input type="submit" style="position: absolute; height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" tabindex="-1" />
				<p class="ilabel" style="margin-top: 16px;">This may take a while. Please be patient.</span></p>
			</form>
			<div id="bottombar" class="loginbar noselect" onclick="return special_submit();">Generate&nbsp;&nbsp;</div>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
		<script>
			flatpickr("#date", {dateFormat: 'Y-m'});
		</script>
	</body>
</html>