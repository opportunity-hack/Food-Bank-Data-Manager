<?php
/*
meta.php - Template data for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

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
	echo "&nbsp;MCDM DonorTrack &copy;2016 J. Rowley, M. Omo, J. Woo - <a href=\"https://github.com/Opportunity-Hack-2016-AZ/Matthews-Crossing-Data-Manager\">github</a>";
}
?>