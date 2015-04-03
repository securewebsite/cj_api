<?php

class cj_links extends cj_xml_api {

	private $capture_keys = array(
		'advertiser-id',
		'advertiser-name',
		'category',
		'click-commission',
		'language',
		'lead-commission',
		'description',
		'destination',
		'link-id',
		'link-name',
		'link-type',
		'performance-incentive',
		'promotion-start-date',
		'promotion-end-date',
		'promotion-type',
		'coupon-code',
		'relationship-status',
		'sale-commission',
		'seven-day-epc',
		'three-month-epc',
		'clickUrl',
	);

	private $number_keys = array(
		'advertiser-id',
		'click-commission',
		'link-id',
		'seven-day-epc',
		'three-month-epc'
	);

	private $links = array();

	private $xml = null;

	public $results = 0;

	public $page = 1;

	public function get($page = 1)
	{
		$this->page = $page;
		$this->_call_api();
		$this->_parse_xml();
		$this->_filter();

		return $this->links;
	}

	private function _call_api()
	{
		$result = $this->curl('https://linksearch.api.cj.com/v2/link-search?website-id=517038&advertiser-ids=joined&link-type=text%20link&keywords=homepage+home%2Bpage+main%2Bpage+primary+generic+logo%2Blink+logo%2Btext+basic%2Blink+general%2Btext+general%2Blink+general%2Blanding+default+redirect+brand+logo&language=en&records-per-page=100&page-number=' . $this->page);

		if ($result)
		{
			$this->xml = new SimpleXMLElement($result) or die("Error: Cannot Parse XML");

			$this->results = $this->xml->links->attributes()['records-returned']->__toString();
			
			$this->false = true;
		}
		else
		{
			$this->error = true;
		}
	}

	private function _parse_xml()
	{
		$this->links = array();

		if (isset($this->xml->links))
		{
			foreach ($this->xml->links->link as $link)
			{
				$new_item = new stdClass;

				foreach ($this->capture_keys as $key)
				{
					if (in_array($key, $this->number_keys) && ! is_numeric($link->$key->__toString()))
					{
						$new_item->{str_replace('-', '_', $key)} = null;
					}
					else
					{
						$new_item->{str_replace('-', '_', $key)} = $link->$key->__toString();
					}
				}

				if ($this->_is_included($new_item))
				{
					$this->links[] = $new_item;
				}
			}
		}
	}

	private function _filter()
	{

	}

	private function _is_included($advertiser)
	{
		return true;
	}

}