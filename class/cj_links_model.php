<?php

class cj_links_model extends mydb {

	protected $links = 'cj_links';

	public function get_all()
	{
		return $this->_get_all($this->links);
	}

	public function insert_batch($links)
	{
		if (is_array($links) && count($links))
		{
			foreach ($links as $link)
			{
				$this->_insert($this->links, $link);
			}
			
			return true;
		}

		return false;
	}

}