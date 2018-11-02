<?php

/*
CXA\DashboardInterface (dashboard.php)

This class provides an interace to the dashboard database.
Presently, only a method to retrieve the current URL for a given frame is provided.

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

namespace CXA;

class DashboardInterface
{
	function __construct($dbconn)
	{
		$this->dbconn = $dbconn;
		$this->get_latest_frame_stmt = $this->dbconn->prepare('SELECT (`url`) FROM `dashboard_data` WHERE `frame_id` = ? ORDER BY `datetime` DESC LIMIT 1;');
	}

	public function get_latest_frame($frame_id)
	{
		$result = "";
		$this->get_latest_frame_stmt->bind_param("i", $frame_id);
		$this->get_latest_frame_stmt->bind_result($result);
		$this->get_latest_frame_stmt->execute();
		$this->get_latest_frame_stmt->fetch();
		return $result;
	}
	
}

?>