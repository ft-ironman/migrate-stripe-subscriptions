# Migrate subscriptions from Source to Destination
Useful when migrating Stripe country accounts. For example, USA to Canada.

## Pre-req
```
composer init
composer require stripe/stripe-php
```

## Run Scripts
```
1.Update API key $source_sk_live
2.Set subscriptions file path $csv_file_path

Run Scripts: 
php migrate_subscriptions.php
php cancel_subscriptions.php
php migrate_invoices.php
```
