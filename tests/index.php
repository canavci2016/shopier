<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CanAvci\Shopier\Shopier;
use CanAvci\Shopier\BillingAddress;
use CanAvci\Shopier\ShippingAddress;
use CanAvci\Shopier\Person;

define("ORDER_ID", uniqid());
define("ORDER_AMOUNT", rand(100, 4000));
define("CALLBACK_URL", "https://www.canavci.com/ccc");


$shopier = new Shopier("q2eq2", "q2eq2e");


$buyer = new Person;
$buyer->setName("can");
$buyer->setSurname("avci");
$buyer->setEmail("test@testmail.com");
$buyer->setPhone("5364778591");


$billingAddress = new BillingAddress;
$billingAddress->setCountry("turkey");
$billingAddress->setCity("istanbul");
$billingAddress->setAddress("Kartaltepeme Mah");
$billingAddress->setPostcode("34200");


$shippingAddress = new ShippingAddress;
$shippingAddress->setCountry("turkey");
$shippingAddress->setCity("istanbul");
$shippingAddress->setAddress("Kartaltepeme Mah");
$shippingAddress->setPostcode("34120");


$shopier->setBuyer($buyer);
$shopier->setBillingAddress($billingAddress);
$shopier->setShippingAdress($shippingAddress);


$shopier->fields(ORDER_ID, ORDER_AMOUNT, CALLBACK_URL);

die($shopier->run(ORDER_ID, ORDER_AMOUNT, CALLBACK_URL));
