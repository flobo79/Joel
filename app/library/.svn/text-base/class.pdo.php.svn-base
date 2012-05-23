<?php

class DBA {
	private $connection;
	static private $instance;
	
	public static function factory($dsn, $username, $password) {
		if (self::$instance) {
			throw new Exception('Database already initialized');
		}
		try {
			self::$instance = new DBA();
			self::$instance->connection = new PDO($dsn, $username, $password);
		} catch (PDOException $e) {
			throw new Exception('Connection failed: ' . $e->getMessage());
		}
	}
	
	public static function getAdapter() {
		return self::$instance;
	}
	
	function fetchRow($table, $where = null, $param = null) {
		$sql = "SELECT * FROM $table";
		if ($where) {
			$where = str_replace('?', $this->connection->quote($param), $where);
			$sql .= " WHERE $where";
		}

		$result = array();
		foreach ($this->connection->query($sql) as $row) {
			$result = $row;
			break;
		}
		return $result;
	}
	
	function query($sql) {
		$this->connection->query($sql);
	}
	
	function fetchQuery($sql) {
		$result = array();
		foreach ($this->connection->query($sql) as $row) {
			$result = $row;
			break;
		}

		return $result;
	}
	
	function quote ($string) {
		return $this->connection->quote($string);
	}
	
	function insert($table, $data) {
		$sql = "INSERT INTO $table(";
		$sql .= join(', ', array_keys($data));
		$sql .= ') VALUES(';
		foreach($data as $value) {
			$sql .= $this->connection->quote($value).', ';
		}
		$sql = trim($sql, ', ').');';
		
		return $this->connection->query($sql);
	}
	
	function update($table, $data, $where) {
		$sql = "UPDATE $table SET ";
		foreach($data as $field => $value) {
			$sql .= $field . ' = ' . $this->connection->quote($value).', ';
		}
		$sql = trim($sql, ', ').' WHERE '.$where;

		return $this->connection->query($sql);
	}
	
	function fetchAll($table, $where = null, $order = null, $limit = null) {
		$sql = "SELECT * FROM $table";
		if ($where) {
			$sql .= " WHERE $where";
		}
		if ($limit) {
			$sql .= " LIMIT $limit";
		}
		if ($order) {
			$sql .= " ORDER $order";
		}
		$sql;
		
		$result = array();
		foreach ($this->connection->query($sql) as $row) {
			$result[] = $row;
		}
		return $result;
	}
	
	function delete($table, $where = null, $param = null, $limit = null) {
		$sql = "DELETE FROM $table";
		if ($where) {
			$where = str_replace('?', $this->connection->quote($param), $where);
			$sql .= " WHERE $where";
		}
		if ($limit) {
			$sql .= " LIMIT $limit";
		}
		
		$result = $this->connection->query($sql);
		return $result->rowCount();
	}
	
	private function __construct() {}
}