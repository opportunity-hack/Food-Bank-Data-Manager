<?php
namespace CXA\TMS;
require_once('Table.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/cxa/php/session.php');

interface Action
{
	public function run($data);
}

trait TableActionAuth
{
	protected function find_auth_level()
	{
		if (isset($this->table->config['data'][$this->auth_source]))
		{
			return (int)($this->table->config['data'][$this->auth_source]);
		}
		else if (isset($this->table->config['data']['table_auth']))
		{
			return (int)($this->table->config['data']['table_auth']);
		}
		else
		{
			return 4;
		}
	}
}

trait ColumnActionAuth
{
	protected function find_auth_level()
	{
		if (isset($this->table->config['columns'][$this->column_name][$this->auth_source]))
		{
			$this->auth_level = (int)($this->table->config['columns'][$this->column_name][$this->auth_source]);
		}
		else if (isset($this->table->config['data']['table_auth']))
		{
			$this->auth_level = (int)($this->table->config['data']['table_auth']);
		}
		else
		{
			$this->auth_level = 4;
		}
	}
}

abstract class BaseAction implements Action
{
	protected $table;
	protected $auth_level;
	protected $auth_source = '';
	
	function __construct(Table $table)
	{
		global $conn;
		$this->table = $table;
		$this->auth_level = $this->find_auth_level();
	}
	
	abstract protected function find_auth_level();
	
	public function run($data)
	{
		boot_user($this->auth_level);
		error_log("Auth level: ".$this->auth_level);
	}
}

abstract class DirectAction extends BaseAction
{
	protected $query;
	
	function __construct(Table $table)
	{
		parent::__construct($table);
		
		$this->query = $this->build_query();
	}
	
	abstract protected function build_query();
	
	protected function query()
	{
		global $conn;
		
		$result = $conn->query($this->query);
		if (!$result)
		{
			error_log('Error #'.$conn->errno.' while running query '.$this->query);
			http_response_code(500);
			echo('database error');
			exit();
		}
		
		return $result;
	}
	
	public function run($data)
	{
		parent::run($data);
		
		return $this->query();
	}
}

class GetAction extends DirectAction
{
	use TableActionAuth;
	
	protected $auth_source = 'get_auth';
	
	protected function build_query()
	{
		$columns = $this->table->get_columns();
		$query = "SELECT ";
		
		foreach ($columns as $column)
		{
			$db_col_name = $column->get_name();
			
			if ($db_col_name !== null)
			{
				$query .= "`".$db_col_name."`, ";
			}
		}
		
		$query = rtrim($query, ", ");
		$query .= " FROM `".$this->table->get_schema_name()."`;";
		
		return $query;
	}
	
	protected function output($result)
	{
		$results = array();
		while ($row = ($result->fetch_assoc()))
		{
			$results[] = $row;
		}
		
		header("Content-Type: application/json");
		echo(json_encode($results));
		exit();
	}
	
	public function run($data)
	{
		$result = parent::run($data);
		
		$this->output($result);
	}
}

class GetUsers extends GetAction
{
	protected function output($result)
	{
		$results = array();
		while ($row = ($result->fetch_assoc()))
		{
			if ($row['userid'] == 777)
			{
				// Ignore guest user
				continue;
			}
			
			$results[] = $row;
		}
		
		header("Content-Type: application/json");
		echo(json_encode($results));
		exit();
	}
}

class NewAction implements Action
{
	public function run($data)
	{
		
	}
}

class SetAction implements Action
{
	public function run($data)
	{
		
	}
}

class DelAction implements Action
{
	public function run($data)
	{
		
	}
}

class ApproveUserAction implements Action
{
	public function run($data)
	{
		
	}
}

class ResetPasswordAction implements Action
{
	public function run($data)
	{
		
	}
}

?>
