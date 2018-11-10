<?php
/*
tables.php - Table management system, main file.
Copyright (c) 2016 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

namespace CXA\TMS;
require_once('Table.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cxa/php/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/cxa/meta.php');

function load_tables_config()
{
	// Load in the table configuration file
	
	$json = file_get_contents('./tableconfig.json');
	$tables_config = json_decode($json, true);
	
	if ($tables_config === NULL)
	{
		// JSON could not be loaded, quit
		
		error_log('JSON Decode Error: '.json_last_error());
		http_response_code(500);
		echo('configuration error');
		exit();
	}
	else
	{
		return $tables_config;
	}
}

function build_actions($tables_config)
{
	// Transform table configuration into the necessary Action classes
	
	$tables = array();
	$actions = array();
	
	foreach ($tables_config as $table_name => $table_config)
	{
		// Create Table class for each table, which will contain actions
		
		$tables[$table_name] = new Table($table_config);
		$table_actions = $tables[$table_name]->get_actions();
		
		if (!empty(array_intersect_assoc($actions, $table_actions)))
		{
			// Same action name defined twice, quit
			
			error_log('Duplicate action name in table: '.$table_name);
			http_response_code(500);
			echo('configuration error');
			exit();
			
		}
		
		// Put all of table's actions into the main action array
		
		$actions = array_merge($actions, $table_actions);
	}
	
	return $actions;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['action']))
{
	// Handle actions (CRUD, reset, etc.)
	
	$action = $_POST['action'];
	$tables_config = load_tables_config();
	$actions = build_actions($tables_config);
	
	if(empty($actions[$action]))
	{
		// Requested action not defined, quit
		
		http_response_code(404);
		echo('unknown action');
		exit();
	}
	
	// Dispatch action
	
	$actions[$action]->run($_POST['data']);
	exit();
}
elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['QUERY_STRING']))
{
	// Send table frontend to client
	
	$table_name = $_SERVER['QUERY_STRING'];
	$tables_config = load_tables_config();
	
	if (!empty($tables_config[$table_name]))
	{
		$table = $tables_config[$table_name];
		
		boot_user($table['data']['table_auth']);
		
		include('./table_template.php');
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
