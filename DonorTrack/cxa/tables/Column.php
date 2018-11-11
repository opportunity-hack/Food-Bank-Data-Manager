<?php
namespace CXA\TMS;
require_once('Table.php');

interface Column
{
	public function __construct($name, array $config);
	public function process($value);
	public function get_name();
	public function get_actions();
	public function get_bind_type();
}

abstract class BaseColumn implements Column
{
	protected $name;
	protected $bind_type = 's';
	
	public $mandatory = true;
	
	function __construct($name, array $config)
	{
		$this->name = $name;
		
		if (isset($config['mandatory']))
		{
			$this->mandatory = (bool)($config['mandatory']);
		}
	}
	
	protected function validate($value)
	{
		if (!$this->mandatory || !empty($value))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	abstract public function process($value);
	
	public function get_name()
	{
		return $this->name;
	}
	
	public function get_actions()
	{
		return array();
	}
	
	public function get_bind_type()
	{
		return $this->bind_type;
	}
}

class ClientColumn implements Column
{
	protected $bind_type = '';
	
	function __construct($name, array $config)
	{
		// Pass
	}
	
	public function process($value)
	{
		// This column should never have any data.
		
		return null;
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
	
	public function get_bind_type()
	{
		return $this->bind_type;
	}
}

class Number extends BaseColumn
{
	protected $bind_type = 'd';
	
	protected function validate($value)
	{
		if (parent::validate($value) && is_numeric($value))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function process($value)
	{
		if (!$this->validate($value))
		{
			http_response_code(400);
			echo('invalid input for '.$this->name);
			exit();
		}
		
		return floatval($value);
	}
}

class Integer extends BaseColumn
{
	protected $bind_type = 'i';
	
	protected function validate($value)
	{
		if (parent::validate($value) && ctype_digit($value))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function process($value)
	{
		if (!$this->validate($value))
		{
			http_response_code(400);
			echo('invalid input for '.$this->name);
			exit();
		}
		
		return intval($value);
	}
}

class Text extends BaseColumn
{
	public function process($value)
	{
		if (!$this->validate($value))
		{
			http_response_code(400);
			echo('invalid input for '.$this->name);
			exit();
		}
		
		return $value;
	}
}

class Password extends Text
{
	public function process($value)
	{
		if (!$this->validate($value))
		{
			http_response_code(400);
			echo('invalid input for '.$this->name);
			exit();
		}
		
		return password_hash($value, PASSWORD_BCRYPT);
	}
}

class EditButton extends ClientColumn
{
	
}

class Approver extends ClientColumn
{
	
}

?>
