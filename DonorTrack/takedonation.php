<?php
include('cxa/php/session.php');
include('cxa/meta.php');
boot_user(2);
include('donorinter.php');

$donation_types = Array(
	"",
	"Individual Donor",
	"Churches/Places of Worship",
	"Business/Corporation/Organization",
	"Government/DES",
	"Purchased Food",
	"Food Waste",
	"Food Drive"
);

function hasError($field){
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(tryField($field)==""){
			return " haserror";
		}else{
			return "";
		}
	}
}

if(!empty($_POST["donorid"]) && !empty($_POST["weight"]) && !empty($_POST["type"])){
	if(addDonation($_POST)){
		$recorded = true;
	}else{
		$recorded = false;
		$interError = true;
	}
}elseif(array_key_exists(tryField("donorid"), $_SESSION["donorlist"])){
	$recorded = false;	
}else{
	error_log("Invalid or missing Donor ID!");
	header("Location: /index.php");
	exit();
}
?>
<html>
	<head>
		<title>Record Donation - MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-ui.css">
		<link rel="icon" type="image/png" href="cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
		<script src="https://unpkg.com/flatpickr"></script>
	</head>
	<body>
		<div id="main" style="min-height: 200px;">
			<div id="topbar" class="loginbar noselect">
				<?php cxa_header() ?>
			</div>
			<div class="welcomebar">
				Record Donation <?php echo $recorded ? "Success" : "- Weight required"; ?><br/>
			</div>
			<?php 
				if($recorded){
					echo '<div id="results" style="width: 100%; border-bottom: 1px solid #aaa; overflow-y: hidden; height: auto;">';
					echo '<div class="resitem nohover"></div>';
					echo '<div class="resitem nohover">';
					echo '<p class="resleft">'.$_SESSION["donorlist"][tryField("donorid")]["firstname"].' '.$_SESSION["donorlist"][tryField("donorid")]["lastname"].'</p>';
					echo '<p class="resright">'.$_SESSION["donorlist"][tryField("donorid")]["email"].'</p>';
					echo '<p class="resleft">'.$donation_types[intval($_POST["type"])].'</p>';
					echo '<p class="resleft">'.$_POST["date"].'</p>';
					//echo '<p class="resleft">'.$_POST["source"].'</p>';
					echo '<p class="resright">'.$_POST["weight"].' lbs</p>';
					echo '</div>';
					echo '<div class="resitem nohover">Donation recorded.</div>';
					echo '</div>';
			?>
				<div id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 15px; margin-top: 5px;">
					<div class="loginbutton" style="width: 260px;" onclick="window.location.assign('/index.php')">
						Back to Menu
					</div>
					<span class="logincenter">- or -</span>
					<div class="loginbutton" style="width: 260px;" onclick="window.location.assign('./takedonation.php?donorid=<?php echo $_POST["donorid"] ?>')">
						Accept Donation from this Donor
					</div>
				</div>
			<?php
				}elseif(isset($interError)){
			?>
				<div id="results" style="width: 100%; border-bottom: 1px solid #aaa; overflow-y: hidden; height: auto;">
					<div class="resitem nohover"></div>
					<div class="resitem nohover">
						Internal error!
					</div>
				</div>
				<div id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 15px; margin-top: 5px;">
					<div class="loginbutton" style="width: 260px;" onclick="window.location.assign('/index.php')">
						Back to Menu
					</div>
					<span class="logincenter">- or -</span>
					<div class="loginbutton" style="width: 260px;" onclick="window.location.assign('/adddonor.php')">
						Try Again
					</div>
				</div>			
			
			<?php
				}else{
					echo '<div id="results" style="width: 100%; border-bottom: 1px solid #aaa; overflow-y: hidden; height: auto;">';
					echo '<div class="resitem nohover"></div>';
					echo '<div class="resitem nohover">';
					echo '<p class="resleft">'.$_SESSION["donorlist"][tryField("donorid")]["firstname"].' '.$_SESSION["donorlist"][tryField("donorid")]["lastname"].'</p>';
					echo '<p class="resright">'.$_SESSION["donorlist"][tryField("donorid")]["email"].'</p>';
					echo '</div>';
					echo '</div>';
			?>
				<form action="takedonation.php" method="post" id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 40px;">
					<input type="hidden" name="donorid" value="<?=tryField("donorid")?>" />
					<p class="ilabel">Donation Type</p>
					<select name="type" class="registertext<?=hasError("type")?>" style="width: 100%;">
						<?php
						$prevval = tryField("type");
						foreach($donation_types as $pos=>$type){
							if(strval($pos) === $prevval){
								echo "<option value=\"$pos\" selected=\"selected\">$type</option>";
							}else{
								echo "<option value=\"$pos\">$type</option>";
							}
						}
						?>
					</select><!--
					<p class="ilabel">Donation Source</p>
					<input type="text" name="source" class="registertext" style="width: 100%;" <?=tryFieldValue("source")?>/>-->
					<p class="ilabel">Donation Weight</p>
					<input type="number" name="weight" class="registertext<?=hasError("weight")?>" style="width: 100%;" <?=tryFieldValue("weight")?>/>
					<p class="ilabel">Donation Date <span style="color: #666">(YYYY-MM-DD)</span></p>
					<input type="text" id="date" name="date" class="registertext" style="width: 100%;" <?=tryField("date")?tryFieldValue("date"):'value="'.date("Y-m-d").'"'?>/>
					<input type="submit" style="position: absolute; height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" tabindex="-1">
				</form>
				<div id="bottombar" class="loginbar noselect" onclick="document.getElementById('login').submit(); return false;">Submit&nbsp;&nbsp;</div>
			<?php
				}
			?>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
		<script>
			flatpickr("#date");
		</script>
	</body>
</html>
<?php
$conn->close();
?>