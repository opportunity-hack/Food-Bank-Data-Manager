<?php
namespace CXA\TMS;
require_once('Table.php');

interface Column
{
	public function __construct($name, array $config);
	public function validate($value);
	public function get_name();
	public function get_actions();
}

class BaseColumn implements Column
{
	private $name;
	private $mandatory = true;
	
	function __construct($name, array $config)
	{
		$this->name = $name;
		
		if (isset($config['mandatory']))
		{
			$mandatory = (bool)($config['mandatory']);
		}
	}
	
	public function validate($value)
	{
		if (!$mandatory || !empty($value))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function get_name()
	{
		return $this->name;
	}
	
	public function get_actions()
	{
		return array();
	}
}

class ClientColumn implements Column
{
	function __construct($name, array $config)
	{
		// Pass
	}
	
	public function validate($value)
	{
		// Data should never be present to be validated against.
		
		return false;
	}
	
	public function get_name()
	{
		// Null name will indicate no associated DB column.
		
		return null;
	}
	
	public function get_actions()
	{
		return array();
	}
}

class Number extends BaseColumn
{
	
}

class Integer extends BaseColumn
{
	
}

class Text extends BaseColumn
{
	
}

class Password extends BaseColumn
{
	
}

class EditButton extends ClientColumn
{
	
}

class Approver extends ClientColumn
{
	
}

?>
