<?php

class cj_advertisers extends cj_xml_api {

	private $capture_keys = array('advertiser-id','account-status','seven-day-epc','three-month-epc','language','advertiser-name','program-url','relationship-status','network-rank','primary-category');

	private $number_keys = array('advertiser-id','seven-day-epc','three-month-epc','network-rank');

	private $advertisers = array();

	private $xml = null;

	public $results = 0;

	public $page = 1;

	public $error = false;

	public function get($page = 1)
	{
		$this->page = $page;
		$this->_call_api();
		$this->_parse_xml();
		$this->_filter();

		return $this->advertisers;
	}

	private function _call_api()
	{
		$result = $this->curl('https://advertiser-lookup.api.cj.com/v3/advertiser-lookup?advertiser-ids=notjoined&records-per-page=100&page-number=' . $this->page);

		if ($result)
		{
			$this->xml = new SimpleXMLElement($result) or die("Error: Cannot Parse XML");

			$this->results = $this->xml->advertisers->attributes()['records-returned']->__toString();
			
			$this->false = true;
		}
		else
		{
			$this->error = true;
		}
	}

	private function _parse_xml()
	{
		$this->advertisers = array();

		if (isset($this->xml->advertisers))
		{
			foreach ($this->xml->advertisers->advertiser as $advertiser)
			{
				$adv_item = new stdClass;

				foreach ($this->capture_keys as $key)
				{
					if ($key == 'primary-category')
					{
						$adv_item->parent = $advertiser->$key->parent->__toString();
						$adv_item->child = $advertiser->$key->child->__toString();
					}
					else if (in_array($key, $this->number_keys) && ! is_numeric($advertiser->$key->__toString()))
					{
						$adv_item->{str_replace('-', '_', $key)} = null;
					}
					else
					{
						$adv_item->{str_replace('-', '_', $key)} = $advertiser->$key->__toString();
					}
				}

				if ($this->_is_included($adv_item, $advertiser))
				{
					$this->advertisers[] = $adv_item;
				}
			}
		}
	}

	private function _filter()
	{
		//get uri for affiliated domain
		foreach ($this->advertisers as &$item)
		{
			$item->advertiser_parent = null;
		}
	}

	private function _is_included($advertiser, $row)
	{
		// don include item with status not equal to actiove
		if (isset($advertiser->account_status) && strtolower($advertiser->account_status) != 'active')
		{
			return false;
		}

		// allow en language only
		if (isset($advertiser->language) && $advertiser->language != 'en')
		{
			return false;
		}

		// store with domain
		/*if (isset($advertiser['program_url']))
		{
			$url = parse_url($advertiser['program_url']);
		}
		*/

		// dont allow location with 

		return true;
	}

}