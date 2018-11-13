<?php
namespace CXA\TMS;
require_once('Column.php');
require_once('Action.php');

class Table
{
	private $columns = array();
	private $actions = array();
	private $schema_name;
	private $primary_key;
	
	public $config;
	
	function __construct(array $table_config)
	{
		global $TMS_COLUMNS;
		$this->config = $table_config;
		
		$this->schema_name = $this->config['data']['schema'];
		
		foreach ($this->config['columns'] as $column_name => $column_config)
		{
			$column_class = __NAMESPACE__ . '\\' . $column_config['cell_class'];
			
			if (!class_exists($column_class))
			{
				error_log('Unknown column class '.$column_class.' in table for schema '.$this->schema_name);
				http_response_code(500);
				echo('configuration error');
				exit();
			}
			
			$this->columns[$column_name] = new $column_class($column_name, $this);
			
			$column_actions = $this->columns[$column_name]->get_actions();
			if (!empty(array_intersect_assoc($this->actions, $column_actions)))
			{
				// Same action name defined twice, quit
				
				error_log('Duplicate action name in table for schema '.$this->schema_name);
				http_response_code(500);
				echo('configuration error');
				exit();
				
			}
			$this->actions = array_merge($this->actions, $column_actions);
		}
		
		if (!isset($this->config['data']['row_pkid'])
			|| !isset($this->config['columns'][$this->config['data']['row_pkid']]))
		{
			error_log('Missing primary key in table for schema '.$this->schema_name);
			http_response_code(500);
			echo('configuration error');
			exit();
		}
		else
		{
			$this->primary_key = $this->config['data']['row_pkid'];
		}
		
		if (isset($this->config['data']['get_action']))
		{
			if (isset($this->config['data']['get_class']))
			{
				$get_class = __NAMESPACE__ . '\\' . $this->config['data']['get_class'];
				
				if (!class_exists($get_class))
				{
					error_log('Unknown action class '.$get_class.' in table for schema '.$this->schema_name);
					http_response_code(500);
					echo('configuration error');
					exit();
				}
				
				$this->actions[$this->config['data']['get_action']] = new $get_class($this);
			}
			else
			{
				$this->actions[$this->config['data']['get_action']] = new GetAction($this);
			}
		}
		
		if (isset($this->config['data']['set_action']))
		{
			if (isset($this->config['data']['set_class']))
			{
				$set_class = __NAMESPACE__ . '\\' . $this->config['data']['set_class'];
				
				if (!class_exists($set_class))
				{
					error_log('Unknown action class '.$set_class.' in table for schema '.$this->schema_name);
					http_response_code(500);
					echo('configuration error');
					exit();
				}
				
				$this->actions[$this->config['data']['set_action']] = new $set_class($this);
			}
			else
			{
				$this->actions[$this->config['data']['set_action']] = new SetAction($this);
			}
		}
		
		if (isset($this->config['data']['del_action']))
		{
			if (isset($this->config['data']['del_class']))
			{
				$del_class = __NAMESPACE__ . '\\' . $this->config['data']['del_class'];
				
				if (!class_exists($del_class))
				{
					error_log('Unknown action class '.$del_class.' in table for schema '.$this->schema_name);
					http_response_code(500);
					echo('configuration error');
					exit();
				}
				
				$this->actions[$this->config['data']['del_action']] = new $del_class($this);
			}
			else
			{
				$this->actions[$this->config['data']['del_action']] = new DelAction($this);
			}
		}
	}
	
	public function get_columns()
	{
		return $this->columns;
	}
	
	public function get_actions()
	{
		return $this->actions;
	}
	
	public function get_schema_name()
	{
		return $this->schema_name;
	}
	
	public function get_primary_key()
	{
		return $this->primary_key;
	}
}
?>
