<?php
namespace CXA\TMS;
require_once('Column.php');
require_once('Action.php');

class Table
{
	private $columns = array();
	private $actions = array();
	private $schema_name;
	
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
			
			$this->columns[$column_name] = new $column_class($column_name, $column_config);
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
}
?>
