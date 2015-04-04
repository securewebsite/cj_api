<?php

class cj_coupons_model extends mydb {

	protected $coupons = 'cj_coupons';
	protected $config = 'cj_config';

	public function get_all()
	{
		return $this->_get_all($this->coupons);
	}

	public function insert_batch($coupons)
	{
		if (is_array($coupons) && count($coupons))
		{
			foreach ($coupons as $link)
			{
				$this->_insert($this->coupons, $coupon);
			}
			
			return true;
		}

		return false;
	}

	public function get_last_timestamp()
	{
		
	}

}