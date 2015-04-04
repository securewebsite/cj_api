<?php

set_time_limit(0);

require_once('class/mydb.php');
require_once('class/cj_xml_api.php');
require_once('class/cj_advertisers.php');
require_once('class/cj_advertisers_model.php');
require_once('class/cj_links.php');
require_once('class/cj_links_model.php');
require_once('class/cj_coupons.php');
require_once('class/cj_coupons_model.php');

/*=======================
      DEBUG FUNCTIONS
======================== */

function dd($data = null)
{
	echo '<pre>' . print_r($data, 1) . '<pre>';
	die();
}

function prints($str = '')
{
	echo $str . PHP_EOL;

	if (ob_get_length() > 0)
	{
		if (ob_end_flush())
		{
			ob_start();
		}
		flush();
	}
}

function print_ln($str = '')
{
	if (isset($_SERVER['HTTP_ACCEPT']))
	{
		$str .= '<br>';
	}

	if (ob_get_length() > 0)
	{

		if (ob_end_flush())
		{
			ob_start();
		}
		flush();
	}
	
	echo $str . PHP_EOL;
}