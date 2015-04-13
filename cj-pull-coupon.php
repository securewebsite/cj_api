<?php

require_once('_includes.php');

$cj_coupons = new cj_coupons;
$cj_coupons_model = new cj_coupons_model;

$cj_coupons->connect('datatransfer.cj.com', '518576', '4wddBRC=');

$cj_coupons_model->update_timestamp('20150327');

$last_timestamp = $cj_coupons_model->get_last_timestamp();

$num_files = $cj_coupons->get_coupon_data($last_timestamp);

if ($num_files)
{
	print_ln('New coupon files found on ftp');
	//loop through the files
	while ($fetch_file_result = $cj_coupons->fetch_coupon_file())
	{
		print_ln('Found file ' . $fetch_file_result['filename'] . ' Timestamp:' . $fetch_file_result['timestamp'] . ' Num Lines:' . $fetch_file_result['num_lines']);
		
		//loop through the items in each file
		while($coupon_item = $cj_coupons->fetch_single_coupon())
		{
			$insert_result = $cj_coupons_model->insert($coupon_item);
			print_ln("Inserting aid " . $coupon_item['aid'] . " " . ($insert_result ? "ok" : "fail"));
		}

		$cj_coupons_model->update_timestamp($fetch_file_result['timestamp']);
	}
}
else
{
	print_ln('No new coupon files found on ftp');
}

echo 'Finish' . PHP_EOL;
$cj_coupons_model->update_timestamp('20150327');