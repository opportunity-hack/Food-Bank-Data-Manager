<?php
include('cxa/php/session.php');
include('cxa/meta.php');
boot_user(2);
include('donorinter.php');

$donorAdded = false;

function hasError(){
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(tryField("first")=="" && tryField("last")==""){
			return " haserror";
		}else{
			return "";
		}
	}
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(!empty($_POST["first"]) || !empty($_POST["last"])){
		$newDonorID = nextDonorID();
		if(addDonor($_POST)){
			$donorAdded = true;
		}else{
			$interError = true;
		}
	}
}
?>
<html>
	<head>
		<title>Add New Donor - MCDM DonorTrack</title>
		<link rel="stylesheet" type="text/css" href="cxa/css/cxa-ui.css">
		<link rel="icon" type="image/png" href="cxa/img/favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			.ilabel.ilabel {
				margin: 0px;
			}
		</style>
	</head>
	<body>
		<div id="main" style="min-height: 200px;">
			<div id="topbar" class="loginbar noselect">
				<?php cxa_header() ?>
			</div>
			<div class="welcomebar">
				Add Donor <?php echo $donorAdded ? "Success" : "- One name required"; ?><br/>
			</div>
			<?php 
				if($donorAdded){
					echo '<div id="results" style="width: 100%; border-bottom: 1px solid #aaa; overflow-y: hidden; height: auto;">';
					echo '<div class="resitem nohover"></div>';
					echo '<div class="resitem nohover">';
					echo '<p class="resleft">'.tryField("first").' '.tryField("last").'</p>';
					echo '<p class="resleft">'.tryField("email").'</p>';
					echo '<p class="resright">'.tryField("street").'</p>';
					if(tryField("town")!="" && tryField("state")!=""){
						echo '<p class="resright">'.tryField("town").', '.tryField("state").' '.tryField("zip").'</p>';
					}
					echo '</div>';
					echo '<div class="resitem nohover">Donor added.</div>';
					echo '</div>';
			?>
				<div id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 15px; margin-top: 5px;">
					<div class="loginbutton" style="width: 260px;" onclick="window.location.assign('/index.php')">
						Back to Menu
					</div>
					<span class="logincenter">- or -</span>
					<div class="loginbutton" style="width: 260px;" onclick="window.location.assign('./takedonation.php?donorid=<?=$newDonorID?>')">
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
			?>
				<form action="adddonor.php" method="post" id="login" style="height: auto; padding: 10px 15px; width: 270px; margin-bottom: 40px;">
					<p class="ilabel">First Name</p>
					<p class="ilabel fright">Last Name</p>
					<input type="text" name="first" class="registertext<?=hasError()?>" style="width: 49%; margin-right: 1%" <?=tryFieldValue("first")?>/><!--
				--><input type="text" name="last" class="registertext<?=hasError()?>" style="width: 49%; margin-left: 1%" <?=tryFieldValue("last")?>/>
					<p class="ilabel">E-Mail Address</p>
					<input type="text" name="email" class="registertext" style="width: 100%;" <?=tryFieldValue("email")?>/>
					<p class="ilabel">Street Address</p>
					<input type="text" name="street" class="registertext" style="width: 100%;" <?=tryFieldValue("street")?>/>
					<p class="ilabel">City</p>
					<input type="text" name="town" class="registertext" style="width: 100%;" <?=tryFieldValue("town")?>/>
					<p class="ilabel">State</p>
					<p class="ilabel fright">ZIP Code</p>
					<select name="state" class="registertext" style="width: 49%; margin-right: 1%;">
						<option value=""></option>
						<?php
						$states = Array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY', 'AE', 'AA', 'AP');
						$prevstate = tryField("state");
						foreach($states as $state){
							if($state == $prevstate){
								echo "<option value=\"$state\" selected=\"selected\">$state</option>";
							}else{
								echo "<option value=\"$state\">$state</option>";
							}
						}
						?>
					</select><!--
				 --><input type="number" name="zip" class="registertext" style="width: 49%; margin-left: 1%" <?=tryFieldValue("zip")?>/><!--
					<p class="ilabel">Phone Number</p>
					<input type="number" name="phone" class="registertext" style="width: 100%;" <?=tryFieldValue("phone")?>/>-->
					<input type="submit" style="position: absolute; height: 0px; width: 0px; border: none; padding: 0px;" hidefocus="true" tabindex="-1">
				</form>
				<div id="bottombar" class="loginbar noselect" onclick="document.getElementById('login').submit(); return false;">Submit&nbsp;&nbsp;</div>
			<?php
				}
			?>
			<div id="footer" class="loginbar"><?php cxa_footer() ?></div>
		</div>
	</body>
</html>
<?php
$conn->close();
?>