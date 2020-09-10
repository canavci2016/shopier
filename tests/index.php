<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CanAvci\Shopier\Shopier;

define("ORDER_ID", uniqid());
define("ORDER_AMOUNT", rand(100, 4000));
define("CALLBACK_URL", "https://www.canavci.com/ccc");


$shopier = new Shopier("q2eq2", "q2eq2e");

$shopier->setBuyer([
    'id' => 23,
    'first_name' => 'can', 'last_name' => 'avci', 'email' => 'test@testmail.com', 'phone' => '5342342312']);
$shopier->setOrderBilling([
    'billing_address' => 'Kartaltepeme Mah',
    'billing_city' => 'istanbul',
    'billing_country' => 'turkey',
    'billing_postcode' => '34200',
]);

$shopier->setOrderShipping([
    'shipping_address' => 'Kartaltepeme Mah',
    'shipping_city' => 'istanbul',
    'shipping_country' => 'turkey',
    'shipping_postcode' => '34200',
]);

die($shopier->run(ORDER_ID, ORDER_AMOUNT, CALLBACK_URL));
