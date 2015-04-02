<?php

class cj_advertisers_model extends mydb {

	protected $advertisers_table = 'cj_advertisers';

	public function get_all()
	{
		return $this->_get_all($this->advertisers_table);
	}

	public function insert_batch($advertisers)
	{
		if (is_array($advertisers) && count($advertisers))
		{
			foreach ($advertisers as $advertiser)
			{
				$this->_insert($this->advertisers_table, $advertiser);
			}
			
			return true;
		}

		return false;
	}

}