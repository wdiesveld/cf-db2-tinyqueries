<?php


/**
 * Wrapper class for db2-functions which behaves as PDO object
 *
 */
class DB2
{
	private $connection;
	
	/**
	 *
	 *
	 */
	public function __construct($connectionString, $user='', $password='')
	{
		$this->connection = db2_connect(
			$connectionString, 
			$user, 
			$password,
			array(
				'autocommit' => DB2_AUTOCOMMIT_OFF
			));
		
		if (!$this->connection)
			throw new \Exception("Cannot connect to DB: " . db2_conn_errormsg());
	}
	
	/**
	 *
	 *
	 */
	public function __destruct()
	{
		if (!$this->connection)
			return;
			
		db2_close( $this->connection );
	}
	
	/**
	 *
	 *
	 */
	public function beginTransaction()
	{
		db2_exec( $this->connection, "BEGIN TRANSACTION" );
	}
	
	/**
	 *
	 *
	 */
	public function commit()
	{
		db2_commit( $this->connection );
	}
	
	/**
	 *
	 *
	 */
	public function lastInsertId()
	{
		return db2_last_insert_id( $this->connection );
	}
	
	/**
	 *
	 *
	 */
	public function prepare($query)
	{
		return new DB2statement( $this->connection, $query );
	}
	
	/**
	 *
	 *
	 */
	public function query($query)
	{
		$sth = new DB2statement( $this->connection, $query );
		
		$sth->execute();
		
		return $sth;
	}
	
	/**
	 *
	 *
	 */
	public function quote($string)
	{
		return db2_escape_string( $string );
	}
	
	/**
	 *
	 *
	 */
	public function rollBack()
	{
		db2_rollback( $this->connection );
	}
	
	/**
	 *
	 *
	 */
	public function setAttribute($attr, $value)
	{
		// dummy
	}
}

/**
 *
 *
 */
class DB2statement
{
	private $connection;
	private $query;
	private $statementHandle;
	
	/**
	 *
	 *
	 */
	public function __construct($connection, $query)
	{
		$this->connection 	= $connection;
		$this->query		= $query;
	}
	
	/**
	 *
	 *
	 */
	public function bindValue( $name, $value, $type )
	{
		$valueSql = db2_escape_string( $value );
		
		switch ($type)
		{
			case \PDO::PARAM_NULL:
				$valueSql = "NULL";
				break;
				
			case \PDO::PARAM_INT: 
				break;
				
			case \PDO::PARAM_STR: 
			default:
				$valueSql = "'" . $valueSql . "'"; 
				break;
		}
		
		$this->query = preg_replace("/" . $name . "(\W)/", $valueSql . "$1", $this->query . " ");
	}
	
	/**
	 *
	 *
	 */
	public function execute()
	{
		$this->statementHandle = db2_prepare( $this->connection, $this->query );
		
		if (!$this->statementHandle)
		{
			$error = $this->errorInfo();
			if ($error && is_array($error) && count($error)>=3)
				throw new \Exception($error[1] . " - " . $error[2]);
			throw new \Exception('unknown error during execution of query');
		}
			
		return db2_execute( $this->statementHandle );
	}
	
	/**
	 *
	 *
	 */
	public function errorInfo()
	{
		return array(
			null,
			db2_stmt_error( $this->statementHandle ),
			db2_stmt_errormsg( $this->statementHandle )
		);
	}
	
	/**
	 *
	 *
	 */
	public function fetch($type)
	{
		switch ($type)
		{
			case \PDO::FETCH_NUM: 
				return db2_fetch_array( $this->statementHandle );
				break;
				
			case \PDO::FETCH_ASSOC: 
				return db2_fetch_assoc( $this->statementHandle ); 
				break;
				
			default: 
				throw new \Exception("Fetch type " . $type . " not supported"); break;
		}
	}
	
	/**
	 *
	 *
	 */
	public function fetchAll($type)
	{
		$rows = array();
		
		while ($row = $this->fetch($type))
			$rows[] = $row;
			
		return $rows;
	}
	
	
}


