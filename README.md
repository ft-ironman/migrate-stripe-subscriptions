# Migrate Subscriptions from Source to Destination
Useful when migrating Stripe country accounts. For example, USA to Canada.

## Pre-req
```
composer init
composer require stripe/stripe-php
```

## Run Script
```
1.Update API key $source_sk_live
2.Set subscriptions file path $csv_file_path

Run Script: 
php migrate-subscriptions.php
```
