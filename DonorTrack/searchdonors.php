<?php
include('cxa/php/session.php');
include('cxa/meta.php');
boot_user(2);
include('donorinter.php');

$donorSearch = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if( !empty($_POST["firstname"]) || !empty($_POST["lastname"]) || !empty($_POST["email"]) ){
		$results = Array();
		$firstname = empty($_POST["firstname"]) ? "" : $_POST["firstname"];
		$lastname = empty($_POST["lastname"]) ? "" : $_POST["lastname"];
		$email = empty($_POST["email"]) ? "" : $_POST["email"];
		foreach($_SESSION["donorlist"] as $donorid => $donor){
			if(	   (!$firstname || stristr($donor["firstname"], $firstname) !== false)
				&& (!$lastname || stristr($donor["lastname"], $lastname) !== false)
				&& (!$email || stristr($donor["email"], $email) !== false)){
				$results[$donorid] = $donor;
			}
		}
		$donorSearch = $results;
	}
}
?>
<html>
	<head>
		<title>Donor Search - MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-ui.css">
		<link rel="icon" type="image/png" href="cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="cxa/js/jquery.min.js"><\/script>')</script>
		<script src="cxa/js/cxa-ui.js"></script>
	</head>
	<body>
		<div id="main" style="min-height: 200px;">
			<div id="topbar" class="loginbar noselect">
				<?php cxa_header() ?>
				<a id="refresher" class="hastip" tip="Refresh cached donor list" style="float: right; margin-right: 8px;" href="/donorinter.php?expire"></a>
			</div>
			<div class="welcomebar">
				Donor Search <?php if(is_array($donorSearch)) echo "Results"; ?><br/>
			</div>
			<?php 
				if(is_array($donorSearch)){
					echo '<div id="results" style="width: 100%; border-bottom: 1px solid #aaa;">';
					echo '<div class="resitem"></div>';
					if(!empty($donorSearch)){
						foreach($donorSearch as $donorid => $donor){
							echo '<a href="/takedonation.php?donorid='.$donorid.'" class="resitem">';
							echo '<p class="resleft">'.$donor["firstname"].' '.$donor["lastname"].'</p>';
							echo '<p class="resright">'.$donor["email"].'</p>';
							echo '</a>';
						}
						echo '<a class="resitem" href="/adddonor.php">';
						echo '<p class="resleft">Add New Donor</p>';
						echo '</a>';
					}else{
						echo '<div class="resitem">No records found.</div>';
					}
					echo '</div>';
				}
			?>
			<form action="searchdonors.php" method="post" id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 40px;">
				<p class="ilabel">First Name</p>
				<p class="ilabel fright">Last Name</p>
				<input type="text" name="firstname" class="registertext" style="width: 49%; margin-right: 1%" /><!--
			 --><input type="text" name="lastname" class="registertext" style="width: 49%; margin-left: 1%" />
				<p class="ilabel">E-Mail Address</p>
				<input type="text" name="email" class="registertext" style="width: 100%;" />
				<input type="submit" style="position: absolute; height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" tabindex="-1">
			</form>
			<div id="bottombar" class="loginbar noselect" onclick="document.getElementById('login').submit(); return false;">Search&nbsp;&nbsp;</div>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>
<?php
$conn->close();
?>