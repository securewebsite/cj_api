<?php

require_once ('_includes.php');

$cj_advertisers_model = new cj_advertisers_model;

$advertisers = $cj_advertisers_model->get_all();

?>

<table>
	<tr>
		<th>id</th>
		<th>advertiser_id</th>
		<th>account_status</th>
		<th>seven_day_epc</th>
		<th>three_month_epc</th>
		<th>language</th>
		<th>advertiser_name</th>
		<th>program_url</th>
		<th>relationship_status</th>
		<th>network_rank</th>
		<th>parent</th>
		<th>child</th>
		<th>advertiser_parent</th>
	</tr>

<?php foreach ($advertisers as $adv): ?>

	<tr>
		<th><?php echo $adv->id ?></th>
		<th><?php echo $adv->advertiser_id ?></th>
		<th><?php echo $adv->account_status ?></th>
		<th><?php echo $adv->seven_day_epc ?></th>
		<th><?php echo $adv->three_month_epc ?></th>
		<th><?php echo $adv->language ?></th>
		<th><?php echo $adv->advertiser_name ?></th>
		<th><?php echo $adv->program_url ?></th>
		<th><?php echo $adv->relationship_status ?></th>
		<th><?php echo $adv->network_rank ?></th>
		<th><?php echo $adv->parent ?></th>
		<th><?php echo $adv->child ?></th>
		<th><?php echo $adv->advertiser_parent ?></th>
	</tr>

<?php endforeach ?>

</table>