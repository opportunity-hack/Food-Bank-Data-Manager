<?php
namespace CXA\TMS;
require_once('Table.php');

interface Column
{
	public function __construct($name, Table $table);
	public function process($value);
	public function get_name();
	public function get_actions();
	public function get_bind_type();
}

abstract class BaseColumn implements Column
{
	protected $name;
	protected $bind_type = 's';
	protected $table;
	
	public $mandatory = true;
	
	function __construct($name, Table $table)
	{
		$this->name = $name;
		$this->table = $table;
		
		if (isset($this->table->config['columns'][$this->name]['mandatory']))
		{
			$this->mandatory = (bool)($this->table->config['columns'][$this->name]['mandatory']);
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
	protected $name;
	protected $bind_type = '';
	protected $table;
	
	function __construct($name, Table $table)
	{
		$this->name = $name;
		$this->table = $table;
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
	
	public function get_actions()
	{
		$config = $this->table->config['columns'][$this->name];
		$actions = array();
		
		if (isset($config['action']))
		{
			if (isset($config['action_class']))
			{
				$action_class = __NAMESPACE__ . '\\' . $config['action_class'];
				
				if (!class_exists($action_class))
				{
					error_log('Unknown action class '.$action_class.' in column '.$this->name);
					http_response_code(500);
					echo('configuration error');
					exit();
				}
				
				$actions[$config['action']] = new $action_class($this->table, $this->name);
			}
			else
			{
				$actions[$config['action']] = new ResetPasswordAction($this->table, $this->name);
			}
		}
		
		return $actions;
	}
}

class EditButton extends ClientColumn
{
	
}

class Approver extends Integer
{
	public function get_name()
	{
		// Null name will indicate no associated DB column.
		
		return null;
	}
	
	public function get_actions()
	{
		$config = $this->table->config['columns'][$this->name];
		$actions = array();
		
		if (isset($config['action']))
		{
			if (isset($config['action_class']))
			{
				$action_class = __NAMESPACE__ . '\\' . $config['action_class'];
				
				if (!class_exists($action_class))
				{
					error_log('Unknown action class '.$action_class.' in column '.$this->name);
					http_response_code(500);
					echo('configuration error');
					exit();
				}
				
				$actions[$config['action']] = new $action_class($this->table, $this->name);
			}
			else
			{
				$actions[$config['action']] = new ApproveUserAction($this->table, $this->name);
			}
		}
		
		return $actions;
	}
}

?>
