<?php
/*
userinter.php - User management database interface for the CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/
include($_SERVER['DOCUMENT_ROOT'].'/cxa/php/session.php');
include($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');

function load_table_config()
{
	// Load in the table configuration file
	
	$json = file_get_contents("./tableconfig.json");
	$table_config = json_decode($json, true);
	
	if ($table_config === NULL)
	{
		// JSON could not be loaded, quit
		
		error_log("JSON Decode Error: ".json_last_error());
		http_response_code(500);
		echo('configuration error');
		exit();
	}
	else
	{
		return $table_config;
	}
}

function load_tables($table_config)
{
	// Transform table configuration into Table and Endpoint classes
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST["action"]))
{
	// Handle actions (CRUD, reset, etc.)
	
	$table_config = load_table_config();
	
}
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['QUERY_STRING']))
{
	// Send configuration to client
	
	$table_name = $_SERVER['QUERY_STRING'];
	$table_config = load_table_config();
	
	if (!empty($table_config[$table_name]))
	{
		$table = $table_config[$table_name];
		
		if (!authorized($table["data"]["table_auth"]))
		{
			http_response_code(403);
			echo('unauthorized');
			exit();
		}
		
		include("./table_template.php");
		exit();
	}
	else
	{
		http_response_code(404);
		echo('unknown table');
		exit();
	}
}
else
{
	http_response_code(400);
	echo('malformed request');
	exit();
}
?>
