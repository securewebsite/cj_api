<?php

class mydb {
	
	private $host = "localhost";
	private $user = "root";
	private $pass = "";
	private $db   = "cj_api";
	private $con;

	public function _get_all($table, $limit = false, $page = 0)
	{
		$data = array();
		$this->_connect();
		$sql = "SELECT * FROM " . $table;

		if ($limit && $page)
		{

		}

		$result = mysqli_query($this->con,$sql);

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
		
		mysqli_query($this->con,$sql);
		$this->_disconnect();
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
		mysqli_close($this->con);
	}

}