<?php

require_once('_includes.php');

$cj_advertisers = new cj_advertisers;
$cj_advertisers_model = new cj_advertisers_model;

$page = 1;
$results_per_page = 100;
$num_results = 100;

while ($num_results >= $results_per_page)
{
	$advertisers = $cj_advertisers->get($page);
	$num_results = $cj_advertisers->results;

	$advertisers_db = $cj_advertisers_model->get_all();

	$adv_new = array();
	$adv_update = array();

	foreach ($advertisers as $advertiser)
	{
		if ($cj_advertisers->_obj_exist($advertiser, $advertisers_db, 'advertiser_id'))
		{
			$adv_update[] = $advertiser;
		}
		else
		{
			$adv_new[] = $advertiser;
		}
	}

	//dd($adv_new);

	$cj_advertisers_model->insert_batch($adv_new);
	
	print_ln('Imported advertisers page: ' . $page);

	//sleep to avoid exceed 20 req/min api limit
	sleep(ceil(60 / 20));

	$page++;
}