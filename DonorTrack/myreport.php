<?php
include('cxa/php/session.php');
include('cxa/meta.php');
boot_user(3);
include('donorinter.php');

if (!empty($_POST["date"]))
{
	$filename = getMYReport($_POST["date"]);
	if ($filename !== false)
	{
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=\"MCDM-MYReport.xlsx\"");
		echo(file_get_contents($filename));
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
		<title>Monthly/Yearly Report - MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-ui.css">
		<link rel="icon" type="image/png" href="cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
		<script src="https://unpkg.com/flatpickr"></script>
		<script>
			function special_submit ()
			{
				var element = document.getElementById('results');
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
				Monthly/Yearly Report<br/>
			</div>
			<?php
			if(isset($interError)){
			?>
				<div id="results" style="width: 100%; border-bottom: 1px solid #aaa; overflow-y: hidden; height: auto;">
					<div class="resitem nohover"></div>
					<div class="resitem nohover">
						Internal error!
					</div>
				</div>
			<?php
				}
			?>
			<form action="myreport.php" method="post" id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 40px;">
				<p class="ilabel">Report Month <span style="color: #666">(YYYY-MM)</span></p>
				<input type="text" id="date" name="date" class="registertext" style="width: 100%;" <?=tryField("date")?tryFieldValue("date"):'value="'.date("Y-m").'"'?> />
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
<?php
$conn->close();
?>