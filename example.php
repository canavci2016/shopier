<?php

	$shopier = new Shopier(API_KEY, API_SECRET);
	$shopier->setBuyer([
		'id' => 23,
		'first_name' => 'can', 'last_name' => 'avci', 'email' => 'canavci2016@gmail.com', 'phone' => '5342342312']);
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
