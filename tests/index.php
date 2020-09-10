<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CanAvci\Shopier\Shopier;
use CanAvci\Shopier\BillingAddress;
use CanAvci\Shopier\ShippingAddress;

define("ORDER_ID", uniqid());
define("ORDER_AMOUNT", rand(100, 4000));
define("CALLBACK_URL", "https://www.canavci.com/ccc");


$shopier = new Shopier("q2eq2", "q2eq2e");


$shopier->setBuyer(['id' => 23, 'first_name' => 'can', 'last_name' => 'avci', 'email' => 'test@testmail.com', 'phone' => '5342342312']);


$billingAddress = new BillingAddress;
$billingAddress->setCountry("turkey");
$billingAddress->setCity("istanbul");
$billingAddress->setAddress("Kartaltepeme Mah");
$billingAddress->setPostcode("34200");

$shopier->setBillingAddress($billingAddress);

$shippingAddress = new ShippingAddress;
$shippingAddress->setCountry("turkey");
$shippingAddress->setCity("istanbul");
$shippingAddress->setAddress("Kartaltepeme Mah");
$shippingAddress->setPostcode("34120");

$shopier->setShippingAdress($shippingAddress);


die($shopier->run(ORDER_ID, ORDER_AMOUNT, CALLBACK_URL));
