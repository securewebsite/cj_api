<?php

$this->coupon_api->connect('datatransfer.cj.com', '518576', '4wddBRC=');

$last_timestamp = $this->coupon_model->get_last_timestamp();

$num_files = $this->coupon_api->get_coupon_data($last_timestamp);

if ($num_files)
{
	print_ln('New coupon files found on ftp');

	//loop through the files
	while ($fetch_file_result = $this->coupon_api->fetch_coupon_file())
	{
		print_ln('Found file ' . $fetch_file_result['filename'] . ' Timestamp:' . $fetch_file_result['timestamp'] . ' Num Lines:' . $fetch_file_result['num_lines']);
		
		//loop through the items in each file
		while($coupon_item = $this->coupon_api->fetch_single_coupon())
		{
			print_ln(print_r($coupon_item,1));
		}

		$this->coupon_model->update_timestamp($fetch_file_result['timestamp']);
	}
}
else
{
	print_ln('No new coupon files found on ftp');
}

echo 'Finish' . PHP_EOL;
$this->coupon_model->update_timestamp('20150327');