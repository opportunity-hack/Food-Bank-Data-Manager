<?php
/*
config.php - Configuration data for the CXA Auth LW web data framework.
Copyright (c) 2018 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/


function env($key, $default=null){
	if(!empty($_SERVER[$key]))
		return $_SERVER[$key];
	else
		return $default;
}

include("secret-config.php");

$send_email = env("APP_EMAIL", false); //Whether to send emails relating to account info
$app_name = "DonorTrack"; //Name of the app you are building, currently used only in emails
$pypath = env("APP_PYTHON", "python"); //The path to Python 2.7, or just "python" if applicable
$appdb_version = 2.01; //Target database schema version
$app_version = "2.0.5"; //Application version
?>
