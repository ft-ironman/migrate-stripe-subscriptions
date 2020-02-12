<?php
$source_sk_live = 'sk_test_xxxxxxxxxxxxxxxxxx';

require_once('vendor/autoload.php');
$log_file = fopen("log_delete_subscriptions.txt", "w");

\Stripe\Stripe::setApiKey($source_sk_live);
fwrite($log_file, "script started" . "\n");

$has_more = true;
$index_of_subscription = 0;
try {
    while($has_more) {
        $subscriptions = \Stripe\Subscription::all(['limit' => 100]);
        foreach($subscriptions as $subscription){
            $index_of_subscription++;
            $subscription_id = $subscription->id;
            $has_more = $subscriptions->has_more;
            $subscription->delete();
            fwrite($log_file, "subscription_id=" . $subscription_id . "\t\tdeleted=true" . "\t\tindex=" . $index_of_subscription . "\n");
        }
    }
} catch (Exception $e) {
    fwrite($log_file, "subscription_id=" . $subscription_id . "\t\tdeleted=false\t\t" . "\t\tindex=" . $index_of_subscription);
    fwrite($log_file, 'Caught exception: ' . $e->getMessage() . "\n");
}

fwrite($log_file, "script finished" . "\n");