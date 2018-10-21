<?php

/*
CXA Database Connector (dbconn.php)

Connects to and configures (if necessary) a MySQL database.

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

use CXA\Util;
require_once "util.php";

$conn = new mysqli($servername.":".$serverport, $username, $password);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$dbavail = !!$conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'")->num_rows;
if(!$dbavail){
	$conn->query("CREATE SCHEMA `".$dbname."` DEFAULT CHARACTER SET utf8mb4");
	Util::run("mysql --user=$username --password=$password --host=$servername --port=$serverport $dbname < ".__DIR__."/sql/bootstrap.sql");
}

$conn->select_db($dbname);

$patches = Array(
	Array(
		"from" => 2.0,
		"to" => 2.01,
		"file" => "/sql/patch_2.0_2.01.sql"
	),
	Array(
		"from" => 0,
		"to" => 2.01,
		"file" => "/sql/bootstrap.sql"
	)
);

$versioned = !!$conn->query("SELECT * FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = 'schema_version' LIMIT 1")->num_rows;
if($versioned) $versioned = !!$conn->query("SELECT `version` FROM `schema_version` LIMIT 1")->num_rows;
$version = 1.0;
if($versioned) $version = floatval($conn->query("SELECT `version` FROM `schema_version` LIMIT 1")->fetch_assoc()["version"]);
if($version < $appdb_version){error_log("Database requires upgrade, $version < $appdb_version.");}

while($version < $appdb_version){
	foreach($patches as $patch){
		if($patch["from"] <= $version && $version < $patch["to"]){
			Util::run("mysql --user=$username --password=$password --host=$servername --port=$serverport $dbname < ".__DIR__.$patch["file"]);
			$version = $patch["to"];
		}
	}
}

?>