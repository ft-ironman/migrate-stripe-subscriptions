<?php
$destination_sk_live = 'sk_test_xxxxxxxxxxxxxxxxxx';
$csv_file_path = "subscriptions.csv";

require_once('vendor/autoload.php');
	
//add API key
\Stripe\Stripe::setApiKey($destination_sk_live);

$log_file = fopen("log_create_subscriptions.txt", "w");
$row = 0;
$subscription = array();
$subscriptions = array();

// create associative array from csv file
if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		$row++;
		if ($row == 1) {
			$columns = $data;
		} else {
			for ($c=0; $c < $num; $c++) {
				$subscription[$columns[$c]] = $data[$c];
			}
			$subscriptions[] = $subscription;
		}
	}
	fclose($handle);
}

//create subscription in new stripe
$index_of_subscription = 0;
foreach($subscriptions as $subscription_item){
    $index_of_subscription++;
	try {
		if ($subscription_item['Status'] == 'active') {
			\Stripe\Subscription::create([
				'customer' => $subscription_item['Customer ID'],
				'items' => [['plan' => $subscription_item['Plan']]],
				'billing_cycle_anchor' => strtotime($subscription_item['Current Period End (UTC)']),
				'prorate' => false
			]);
            fwrite($log_file, "customer_id=" . $subscription_item['Customer ID'] . "\t\tmigrated=true\t\tsubscription=active"  . "\t\tindex=" . $index_of_subscription .  "\n");
		} elseif ($subscription_item['Status'] == 'trialing') {
			\Stripe\Subscription::create([
				'customer' => $subscription_item['Customer ID'],
				'items' => [['plan' => $subscription_item['Plan']]],
				'trial_end' => strtotime($subscription_item['Trial End (UTC)']),
				'prorate' => false
			]);
            fwrite($log_file, "customer_id=" . $subscription_item['Customer ID'] . "\t\tmigrated=true\t\tsubscription=trialing"  . "\t\tindex=" . $index_of_subscription .  "\n");
		} elseif ($subscription_item['Status'] == 'past_due') {
			//ToDo: here we should handle case for users with status "past_due".
            fwrite($log_file, "customer_id=" . $subscription_item['Customer ID'] . "\t\tmigrated=false\t\tsubscription=past_due"  . "\t\tindex=" . $index_of_subscription .  "\n");
		}
	} catch (Exception $e) {
		fwrite($log_file, "customer_id=" . $subscription_item['Customer ID'] . "\t\tmigrated=false\t\t" . "\t\tindex=" . $index_of_subscription);
		fwrite($log_file, 'Caught exception: ' . $e->getMessage() . "\n");
	}
}
fclose($log_file);
