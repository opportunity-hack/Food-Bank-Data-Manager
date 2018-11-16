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
		$this->table = $table;
		$this->auth_level = $this->find_auth_level();
	}
	
	abstract protected function find_auth_level();
	
	public function run($data)
	{
		if(!authorized($this->auth_level))
		{
			error_log('Failed access attempt at level '.$level.' by '.$_SESSION["userdata"]["username"].' with authorization level '.$_SESSION["userdata"]["authorization"].'!');
			http_response_code(403);
			echo('unauthorized');
			exit();
		}
	}
}

class GetAction extends BaseAction
{
	use TableActionAuth;
	
	protected $auth_source = 'get_auth';
	protected $query;
	
	function __construct(Table $table)
	{
		parent::__construct($table);
		
		$this->query = $this->build_query();
	}
	
	protected function build_query()
	{
		$columns = $this->table->get_columns();
		$query = "SELECT ";
		
		foreach ($columns as $column)
		{
			$column_name = $column->get_name();
			
			if ($column_name !== null)
			{
				$query .= "`".$column_name."`, ";
			}
		}
		
		$query = rtrim($query, ", ");
		$query .= " FROM `".$this->table->get_schema_name()."`;";
		
		return $query;
	}
	
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
		parent::run($data);
		
		$result =  $this->query();
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

class SetAction extends BaseAction
{
	use TableActionAuth;
	
	protected $auth_source = 'set_auth';
	protected $statement;
	protected $bound_data = array();
	
	protected function pre_validate($data_columns)
	{
		$columns = $this->table->get_columns();
		$final_columns = array();
		
		foreach ($columns as $column)
		{
			$column_name = $column->get_name();
			
			if ($column_name !== null)
			{
				if (isset($data_columns[$column_name]))
				{
					$final_columns[$column_name] = $column;
				}
				else if ($column->mandatory)
				{
					http_response_code(400);
					echo('missing column '.$column_name);
					exit();
				}
			}
		}
		
		return $final_columns;
	}
	
	protected function build_query($columns)
	{
		$query = "UPDATE `".$this->table->get_schema_name()."` SET ";
		
		foreach ($columns as $column)
		{
			$column_name = $column->get_name();
			
			if ($column_name !== null && $column_name != $this->table->get_primary_key())
			{
				$query .= "`".$column_name."`=?, ";
			}
		}
		
		$query = rtrim($query, ", ");
		$query .= " WHERE `".$this->table->get_primary_key()."`=? LIMIT 1;";
		
		return $query;
	}
	
	protected function setup_bindings($columns)
	{
		$bind_types = "";
		$params = array("");
		
		foreach ($columns as $column)
		{
			$column_name = $column->get_name();
			
			if ($column_name !== null && $column_name != $this->table->get_primary_key())
			{
				$bind_types .= $column->get_bind_type();
				
				$this->bound_data[$column_name] = null;
				$params[] = &$this->bound_data[$column_name];
			}
		}
		
		$bind_types .= $columns[$this->table->get_primary_key()]->get_bind_type();
		$this->bound_data[$this->table->get_primary_key()] = null;
		$params[] = &$this->bound_data[$this->table->get_primary_key()];
		$params[0] = $bind_types;
		
		return call_user_func_array(array($this->statement, 'bind_param'), $params);
	}
	
	protected function execute()
	{
		global $conn;
		
		if (!$this->statement->execute())
		{
			error_log('Error #'.$conn->errno.' while executing statement.');
			http_response_code(500);
			echo('database error');
			exit();
		}
	}
	
	public function run($data)
	{
		global $conn;
		parent::run($data);
		
		$columns = $this->pre_validate($data);
		$query = $this->build_query($columns);
		$this->statement = $conn->prepare($query);
		
		if (!$this->statement)
		{
			error_log('Error #'.$conn->errno.' while preparing query '.$this->query);
			http_response_code(500);
			echo('database error');
			exit();
		}
		
		if(!$this->setup_bindings($columns))
		{
			error_log('Error #'.$conn->errno.' while configuring statement bindings.');
			http_response_code(500);
			echo('database error');
			exit();
		}
		
		foreach ($columns as $column)
		{
			$column_name = $column->get_name();
			
			if ($column_name !== null)
			{
				$this->bound_data[$column_name] = $column->process($data[$column_name]);
			}
		}
		
		$this->execute();
		
		echo('ok');
		exit();
	}
}

class NewAction implements Action
{
	public function run($data)
	{
		
	}
}

class DelAction extends BaseAction
{
	use TableActionAuth;
	
	protected $auth_source = 'del_auth';
	protected $statement;
	protected $bound_key = null;
	
	protected function pre_validate($data)
	{
		if (!isset($data[$this->table->get_primary_key()]))
		{
			http_response_code(400);
			echo('missing column '.$this->table->get_primary_key());
			exit();
		}
	}
	
	protected function build_query()
	{
		$query = "DELETE FROM `".$this->table->get_schema_name()."` ";
		$query .= " WHERE `".$this->table->get_primary_key()."`=? LIMIT 1;";
		
		return $query;
	}
	
	protected function setup_bindings()
	{
		$columns = $this->table->get_columns();
		$bind_type = $columns[$this->table->get_primary_key()]->get_bind_type();
		return $this->statement->bind_param($bind_type, $this->bound_key);
	}
	
	protected function execute()
	{
		global $conn;
		
		$query = $this->build_query();
		$this->statement = $conn->prepare($query);
		
		if (!$this->statement)
		{
			error_log('Error #'.$conn->errno.' while preparing query '.$query);
			http_response_code(500);
			echo('database error');
			exit();
		}
		
		if(!$this->setup_bindings())
		{
			error_log('Error #'.$conn->errno.' while configuring statement bindings.');
			http_response_code(500);
			echo('database error');
			exit();
		}
		
		if (!$this->statement->execute())
		{
			error_log('Error #'.$conn->errno.' while executing statement.');
			http_response_code(500);
			echo('database error');
			exit();
		}
	}
	
	public function run($data)
	{
		parent::run($data);
		
		$this->pre_validate($data);
		
		$pk_column = $this->table->get_columns()[$this->table->get_primary_key()];
		$this->bound_key = $pk_column->process($data[$this->table->get_primary_key()]);
		
		$this->execute();
		
		echo('ok');
		exit();
	}
}

class ApproveUserAction extends DelAction
{
	use ColumnActionAuth;
	
	protected $auth_source = 'action_auth';
	protected $column_name;
	protected $bound_auth;
	
	public function __construct(Table $table, $column_name)
	{
		parent::__construct($table);
		
		$this->column_name = $column_name;
	}
	
	protected function pre_validate($data)
	{
		foreach (array($this->column_name, $this->table->get_primary_key()) as $key)
		{
			if (!isset($data[$key]))
			{
				http_response_code(400);
				echo('missing column '.$key);
				exit();
			}
		}
		
		if (!authorized($data[$this->column_name]))
		{
			http_response_code(403);
			echo('cannot assign higher authorization than current user');
			exit();
		}
	}
	
	protected function build_query()
	{
		$query  = "INSERT INTO `users` (`username`, `password`, `name`, `email`, `authorization`) ";
		$query .= "SELECT `username`, `password`, `name`, `email`, ? FROM `user_limbo` WHERE `userid` = ?;";
		return $query;
	}
	
	protected function setup_bindings()
	{
		$columns = $this->table->get_columns();
		$bind_types  = $columns[$this->column_name]->get_bind_type();
		$bind_types .= $columns[$this->table->get_primary_key()]->get_bind_type();
		
		return $this->statement->bind_param($bind_types, $this->bound_auth, $this->bound_key);
	}
	
	public function run($data)
	{
		BaseAction::run($data);
		
		$this->pre_validate($data);
		
		$columns = $this->table->get_columns();
		$this->bound_auth = $columns[$this->column_name]->process($data[$this->column_name]);
		$this->bound_key = $columns[$this->table->get_primary_key()]->process($data[$this->table->get_primary_key()]);
		
		$this->execute();
		
		$associated_action = new DelAction($this->table);
		$associated_action->run($data);
	}
}

class ResetPasswordAction extends DelAction
{
	use ColumnActionAuth;
	
	protected $auth_source = 'action_auth';
	protected $column_name;
	protected $bound_token;
	protected $bound_expires;
	
	public function __construct(Table $table, $column_name)
	{
		parent::__construct($table);
		
		$this->column_name = $column_name;
	}
	
	protected function pre_validate($data)
	{
		if (!isset($data[$this->table->get_primary_key()]))
		{
			http_response_code(400);
			echo('missing column '.$key);
			exit();
		}
	}
	
	protected function build_query()
	{
		$query = "REPLACE INTO `password_reset` (`userid`, `token`, `expires`) VALUES (?, ?, ?);";
		
		return $query;
	}
	
	protected function setup_bindings()
	{
		$columns = $this->table->get_columns();
		$bind_types  = $columns[$this->table->get_primary_key()]->get_bind_type();
		$bind_types .= "si";
		
		return $this->statement->bind_param($bind_types, $this->bound_key, $this->bound_token, $this->bound_expires);
	}
	
	protected function generate_reset_link($authenticator, $userid)
	{
		global $conn, $send_email, $thedomain, $app_name;
		
		$resetLink=(!empty($_SERVER['HTTPS']) ? 'https' : 'http')."://".$_SERVER["HTTP_HOST"]."/cxa/reset.php?token=".strtr(base64_encode($authenticator), '+/=', '-_,');
		
		if ($send_email)
		{
			$userid = $conn->escape_string($userid);
			$result = $conn->query("SELECT * FROM users WHERE userid=$userid");
			
			if ($result && $result->num_rows==1)
			{
				$row=$result->fetch_assoc();
				if (preg_match("/.+@.+/", $row["email"]))
				{
					mail(strip_tags($row["email"]), "$app_name Password Reset",'
					<html><body>
						<h2>'.$app_name.' - Password Reset Link</h2>
						<p>Hi '.explode(' ',trim(strip_tags($_SESSION['userdata']['name'])))[0].',<br/>
						You can reset your password at the following link:<br/>
						<a href="'.$resetLink.'">'.$resetLink.'</a><br/>
						If you have forgotten your username or encounter any problems when resetting your password, please contact your administrator.</p>
						<p>Please do not reply to this email. Your response will not be received.</p>
					</body></html>
					',"From: accounts@".$thedomain."\r\n".
					"Reply-To: noreply@".$thedomain."\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/html; charset=ISO-8859-1\r\n");
				}
			}
		}
		
		return $resetLink;
	}
	
	public function run($data)
	{
		BaseAction::run($data);
		
		$this->pre_validate($data);
		
		$columns = $this->table->get_columns();
		$this->bound_key = $columns[$this->table->get_primary_key()]->process($data[$this->table->get_primary_key()]);
		
		$authenticator = openssl_random_pseudo_bytes(33);
		$this->bound_token = hash('sha256', $authenticator);
		$this->bound_expires = time() + 86400;
		
		$this->execute();
		
		echo($this->generate_reset_link($authenticator, $this->bound_key));
		exit();
	}
}

?>
