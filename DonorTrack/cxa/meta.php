<?php
/*
meta.php - Template data for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

require_once('php/config.php');

$sitetitle="MCDM DonorTrack";
function cxa_header($type=false,$url="/index.php"){
	echo '<a id="toplogo" href="'.$url.'">MCDM DonorTrack</a>';
	if($type){
		echo "&nbsp;&nbsp;".$type;
	}
}
function cxa_minheader($type=false,$url="landing.php"){
	echo '<a id="minlogo" href="'.$url.'"></a>';
	if($type){
		echo "<h1>$type</h1>";
	}
}
function cxa_footer(){
	$app_version = $GLOBALS['app_version'];
	echo "&nbsp;MCDM DonorTrack v$app_version &copy;2018 J. Rowley, M. Omo - <a href=\"https://github.com/2018-Arizona-Opportunity-Hack/Team15-Matthews-Crossing-Data-Manager\">github</a>";
}
?>