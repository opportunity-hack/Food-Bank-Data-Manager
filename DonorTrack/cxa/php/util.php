<?php

/*
CXA\Util (util.php)

This class provides static, general-purpose helper functions which are not
related to any other subsystem.

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

namespace CXA;

class Util
{

	public static function run($command)
	{
		error_log("Running command:\n".$command."\n\n".shell_exec($command));
	}
	
}

?>