<?php

class cj_xml_api {

	protected $auth_key = '0099ebc97eb785a814b458410dfce7201f742241a0ec1fc2f177a7b8f8f96d8330b41b6019f28523aa1d194502895d8e49ca46046e16911d31c8e1e2733877e555/286c06a79394b33674c2349b72ce53de4ec66c4bd196e9c0363742d987cf9dc2f23bf2bdb3ef79e7712b25bac3ee281fcf06b86d6bee555bbc29047d4892c881';

	protected $ch;

	protected $headers = array();

	public function __construct()
	{
		$this->set_headers('authorization', $this->auth_key);
	}

	protected function set_headers($key, $val)
	{
		$this->headers[] = $key . ': ' . $val;
	}

	protected function curl($url = false, $args = array())
	{
		if ( ! $url )
			return false;

		$this->ch = curl_init();

		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

		if (is_array($this->headers) && count($this->headers))
		{
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
		}

		if (count($args))
		{
			curl_setopt($this->ch ,CURLOPT_POST, true);
			curl_setopt($this->ch ,CURLOPT_POSTFIELDS, $args);
		}

		if (curl_error($this->ch) == '' && $result = curl_exec($this->ch))
		{
			return $result;
		}
		
		return false;
	}

	public function _obj_exist($target, $list, $col = false)
	{
		if ( ! $col )
		{
			return false;
		}

		foreach ($list as $item)
		{
			if ($target->$col == $item->$col)
			{
				return true;
			}
		}

		return false;
	}
}