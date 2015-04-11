<?php

class mydb {
	
	private $host = "localhost";
	private $user = "root";
	private $pass = "";
	private $db   = "cj_api";
	private $where = array();
	private $queries = array();
	private $con;

	public function _get_where($table, $where = array())
	{
		if (is_array($where))
		{
			foreach ($where as $key => $val)
			{
				$this->where[$key] = $val;
			}
		}

		return $this->_get_all($table);
	}

	public function _get_all($table, $limit = false, $page = 0)
	{
		$data = array();
		$this->_connect();

		$where_arr = array();

		if (count($this->where))
		{
			foreach ($this->where as $key => $val)
			{
				$where_arr[] = $this->_escape($key, "`") . " = " . $this->_escape($val);
			}
		}

		if ($limit && $page)
		{

		}

		$sql = "SELECT * FROM " . $table;

		if (count($where_arr))
		{
			$sql .= " WHERE " . implode(",", $where_arr);
		}

		$result = $this->_mysqli_query($this->con,$sql);

		if ( ! $result )
		{
			die(mysqli_error($this->con));
		}

		while($row = mysqli_fetch_assoc($result))
		{
			$item = new stdClass;

			foreach ($row as $key => $val)
			{
				$item->$key = isset($val) ? $val : null;
			}

			$data[] = $item;
		}

		$this->_disconnect();
		return $data;
	}

	public function _insert($table, $data)
	{
		$this->_connect();

		$keys = array();
		$values = array();

		foreach ($data as $key => $val)
		{
			$keys[] = $key;
			$values[] = "'" . mysqli_real_escape_string($this->con, $val) . "'";
		}

		$sql = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES (" . implode(',', $values) . ")";

		$this->_mysqli_query($this->con,$sql);
		$this->_disconnect();
	}

	public function _update($table, $new_value, $where)
	{
		$this->_connect();
		$new_value_arr = array();
		$where_arr = array();

		foreach ($new_value as $key => $value)
		{
			$new_value_arr[] = $this->_escape($key, "`") . " = " . $this->_escape($value);
		}

		foreach ($where as $key => $value)
		{
			$where_arr[] = $this->_escape($key, "`") . " = " . $this->_escape($value);
		}

		$new_value_arr = implode(",", $new_value_arr);
		$where_arr = implode(",", $where_arr);

		$sql = "UPDATE $table SET $new_value_arr WHERE $where_arr";

		$this->_mysqli_query($this->con,$sql);
		$this->_disconnect();
	}

	public function _last_query()
	{
		return end($this->queries);
	}

	private function _mysqli_query($con, $sql)
	{
		$this->queries[] = $sql;
		return mysqli_query($con, $sql);
	}

	private function _connect()
	{
		$this->con = mysqli_connect($this->host,$this->user,$this->pass,$this->db);

		if (mysqli_connect_errno()) {
		  die("Failed to connect to MySQL: " . mysqli_connect_error());
		}
	}

	private function _disconnect()
	{
		$this->where = array();
		mysqli_close($this->con);
	}

	private function _escape($str, $qoute = "'")
	{
		return ($qoute ? $qoute : "") . mysqli_real_escape_string($this->con, $str) . ($qoute ? $qoute : "");
	}

}