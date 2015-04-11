<?php

class cj_coupons {

	protected $conn_id;
	protected $server;
	protected $username;
	protected $password;
	protected $connection;
	protected $ftp_dir = 'outgoing/';
	protected $temp_dir = 'temp/';

	protected $fetch_mode = false;
	protected $fetch_file = false;
	protected $fetch_data_type_id = '151896'; //CSV Type
	protected $fetch_files = array();
	protected $fetch_file_index   = 0;
	protected $current_coupon_idx = 1; //use 1 to skip the first line which is the column names
	protected $prev_fetch_file = null;

	protected $capture_cols = array(
		'ADVERTISER',
		'PROGRAMURL',
		'RELATIONSHIPSTATUS',
		'DETAILPAGELINK',
		'AID',
		'NAME',
		'DESCRIPTION',
		'LINKTYPE',
		'TRACKINGURL',
		'IMAGEURL',
		'PROMOTIONTYPE',
		'PROMOTIONSTARTDATE',
		'PROMOTIONENDDATE',
		'GETLINKHTML',
		'COUPONCODE',
		'LINKLANGUAGE'
	);

	protected $cell_separator = ',';

	protected $cols_idx = array();

	public function __construct($server = null, $username = null, $password = null)
	{
		if ( ! empty($server))
		{
			$this->set_server($server);
			$this->set_username($username);
			$this->set_password($password);
		}
	}

	public function set_server($server)
	{
		$this->server = $server;
	}

	public function set_username($username)
	{
		$this->username = $username;
	}

	public function set_password($password)
	{
		$this->password = $password;
	}

	public function connect($server = null, $username = null, $password = null)
	{
		if ( ! empty($server))
		{
			$this->set_server($server);
			$this->set_username($username);
			$this->set_password($password);
		}

		if (is_null($this->server))
		{
			throw new Exception('Invalid FTP Server Configuration');
		}

		$this->_connect();
	}

	private function _connect()
	{
		if ($this->connection)
		{
			return true;
		}

		if ( ! $this->conn_id = @ftp_connect($this->server))
		{
			throw new Exception("Cannot connect to $this->server");
		}

		if ( ! $this->connection = @ftp_login($this->conn_id, $this->username, $this->password) )
		{
			throw new Exception("Cannot login to $this->server");
		}

		return $this->connection;
	}

	public function get_coupon_list_ftp()
	{
		if ( ! $this->_connect() )
		{
			return array();
		}

		$coupon_list = ftp_nlist($this->conn_id, $this->ftp_dir);

		if ( ! is_array($coupon_list) )
		{
			throw new Exception("Bad format on FTP File list");
			
		}

		$data = array();

		$types = array(
			'151889' => 'Full Feed (TAB) file name format',
			'151891' => '(TAB) name format',
			'151896' => '(CSV) name format',
		);

		foreach ($coupon_list as $a)
		{
			$item = new stdClass;
			$item->filename = $a;

			if (strpos($a, '_151889_'))
			{
				$item->type = 'tab-full';
				$item->desc = 'Full Feed (TAB) file name format';
			}
			else if (strpos($a, '_151891_'))
			{
				$item->type = 'tab';
				$item->desc = '(TAB) name format';
			}
			else if (strpos($a, '_151896_'))
			{
				$item->type = 'csv';
				$item->desc = '(CSV) name format';
			}
			else
			{
				$item->type = 'unknown';
				$item->desc = 'unknown';
			}

			$data[] = $item;
		}

		return $data;
	}

	/**
	 * Get coupon data from api
	 * this will activate fetch loop mode
	 * 
	 * optional timestamp option, will disregard old data
	 */
	public function get_coupon_data($last_timestamp = false)
	{
		//$files = $this->get_coupon_list_ftp();
		$files = array('518576_151896_20150326.txt','518576_151896_20150327.txt','518576_151896_20150328.txt');
		//get only csv type
		$csv_files = array();

		if ( ! is_array($files) || ! count($files) )
		{
			throw new Exception("No CSV files found");
		}

		foreach ($files as $file)
		{
			$filename = $file; //$file->filename;
			$type = 'csv'; //$file->type;

			if ($type == 'csv')
			{
				$exploded = explode('.', $filename);
				$exploded = explode('_', $exploded[0]);

				if ( ! isset($exploded[2]) || ! is_numeric($exploded[2]))
				{
					continue;
				}

				$timestamp = $exploded[2];

				if ( ! is_numeric($last_timestamp))
				{
					$last_timestamp = 0;
				}

				if ($last_timestamp < $timestamp)
				{
					$csv_files[] = (object)array(
						'filename'  => $filename,
						'timestamp' => $exploded[2]
					);
				}
			}
		}

		//reset current coupon index
		$current_coupon_idx = 1;

		$this->fetch_files = $csv_files;

		return count($this->fetch_files);
	}

	public function fetch_coupon_file()
	{
		//delete previously downloaded file
		if ( is_file($this->prev_fetch_file))
		{
			unlink($this->prev_fetch_file);
		}

		// no files to be processed
		if ( ! count($this->fetch_files))
		{
			return false;
		}

		// reached the maximum files
		if ( $this->fetch_file_index >= count($this->fetch_files) )
		{
			return false;
		}

		if (! is_dir($this->temp_dir))
		{
			mkdir($this->temp_dir);
		}

		$current_filename = $this->fetch_files[$this->fetch_file_index]->filename;
		$current_timestamp = $this->fetch_files[$this->fetch_file_index]->timestamp;
		$this->prev_fetch_file = $this->temp_dir . $current_timestamp . '_' . $this->_generate_random() . '.txt';

		if ( ! ftp_get($this->conn_id, $this->prev_fetch_file, $this->ftp_dir . $current_filename, FTP_ASCII) )
		{
			return false;
		}

		$num_lines = 0;

		$handle = fopen($this->prev_fetch_file, "r");
		$first_col = true;

		while(!feof($handle))
		{

			$line = fgets($handle);
			$num_lines++;

			//map_col_index
			if ($first_col)
			{
				//first line shows the column names
				$file_cols = explode($this->cell_separator, preg_replace('/\s+/', '', $line));
				$cols_idx = array();

				foreach ($this->capture_cols as $capture_col)
				{
					$cols_idx[array_search($capture_col, $file_cols)] = $capture_col;
				}

				$this->cols_idx = $cols_idx;
			}

			$first_col = false;
		}
		
		$this->fetch_file_index++;
		
		return array(
			'filename' => $current_filename,
			'timestamp' => $current_timestamp,
			'num_lines' => $num_lines
		);
	}

	public function fetch_single_coupon()
	{
		if ( ! is_file($this->prev_fetch_file) )
		{
			return false;
		}

		$handle = fopen($this->prev_fetch_file, "r");
		$counter = 0;
		$line = fgetcsv($handle, 1000);

		while($counter < $this->current_coupon_idx && $line = fgetcsv($handle))
		{
			$counter++;
		}

		$coupon = array();

		$this->current_coupon_idx++;

		if ( ! empty($line))
		{
			foreach ($this->cols_idx as $idx => $col)
			{
				$coupon[strtolower($col)] = isset($line[$idx]) ? $line[$idx] : '';
			}

			return $coupon;
		}

		return false;
	}

	public function _generate_random()
	{
		return base_convert(strrev(str_replace('.', '', uniqid('', true))), 16, 36);
	}

}