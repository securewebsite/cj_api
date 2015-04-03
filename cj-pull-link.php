<?php

require_once('_includes.php');

$cj_links = new cj_links;
$cj_links_model = new cj_links_model;

$page = 1;
$results_per_page = 100;
$num_results = 100;

while ($num_results >= $results_per_page)
{
	$links 		 = $cj_links->get($page);
	$num_results = $cj_links->results;

	$links_db = $cj_links_model->get_all();

	$adv_new = array();
	$adv_update = array();

	foreach ($links as $link)
	{
		if ($cj_links->_obj_exist($link, $links_db, 'link_id'))
		{
			$adv_update[] = $link;
		}
		else
		{
			$adv_new[] = $link;
		}
	}

	$cj_links_model->insert_batch($adv_new);
	
	print_ln('Imported links page: ' . $page);

	//sleep to avoid exceed 20 req/min api limit
	sleep(ceil(60 / 20));

	$page++;
}

/*
while ($num_results >= $results_per_page)
{
	$links = $this->links->get($page);
	$num_results = $this->links->results;

	$advertisers_db = $this->links_model->get_all();

	$links_new = array();
	$links_update = array();

	foreach ($links as $link)
	{
		if ($this->_obj_exist($link, $advertisers_db, 'link_id'))
		{
			$links_update[] = $link;
		}
		else
		{
			$links_new[] = $link;
		}
	}

	$this->links_model->insert_batch($links_new);
	
	print_ln('Imported links page ' . $page . ', saved:' . count($links_new));

	sleep(ceil(60 / $this->req_per_min));

	$page++;
}*/