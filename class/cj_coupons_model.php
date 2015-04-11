<?php

class cj_coupons_model extends mydb {

	protected $coupons = 'cj_coupons';
	protected $config = 'cj_config';
	protected $timestamp_col_key = 'coupon_last_timestamp';

	public function get_all()
	{
		return $this->_get_all($this->coupons);
	}

	public function get_by_id($aid)
	{
		$result = $this->_get_where($this->coupons, array('aid' => $aid));

		if (count($result))
		{
			return $result[0];
		}

		return false;
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
		$data = $this->_get_where($this->config, array('key' => $this->timestamp_col_key));

		if (count($data))
		{
			return $data[0]->value;
		}

		return false;
	}

	public function update_timestamp($timestamp)
	{
		$last_timestamp = $this->get_last_timestamp();

		if ($last_timestamp === false)
		{
			$this->_insert($this->config, array('value' => $timestamp, 'key' => $this->timestamp_col_key));
		}
		else
		{
			$this->_update($this->config, array('value' => $timestamp), array('key' => $this->timestamp_col_key));
		}
	}

}