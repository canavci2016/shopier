<?php

namespace CanAvci\Shopier;

class Shopier
{
    private $payment_url = 'https://www.shopier.com/ShowProduct/api_pay4.php';
    private $api_key, $api_secret, $module_version, $buyer, $currency = 'TRY';
    private $billingAddress;
    private $shippingAdress;

    public function __construct($api_key, $api_secret, $module_version = ('1.0.4'))
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->module_version = $module_version;
    }

    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(BillingAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    public function getShippingAdress(): ShippingAddress
    {
        return $this->shippingAdress;
    }

    public function setShippingAdress(ShippingAddress $shippingAdress)
    {
        $this->shippingAdress = $shippingAdress;
    }

    public function setBuyer(Person $person)
    {
        $this->buyer = $person;
    }

    public function getBuyer(): Person
    {
        return $this->buyer;
    }

    public function run($order_id, $order_total, $callback_url)
    {
        return '<!doctype html>
             <html lang="en">
            <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
     </head>
    <body>
    ' . $this->form($order_id, $order_total, $callback_url) . '
        <script type="text/javascript">document.getElementById("shopier_form_special").submit();</script>
     </body>
    </html>';

    }

    public function form($order_id, $order_total, $callback_url)
    {
        $form = "<form method='post' action='{$this->payment_url}' id='shopier_form_special'>";

        foreach ($this->fields($order_id, $order_total, $callback_url) as $name => $value) {
            $form .= "<input name='{$name}' value='{$value}' type='hidden' />";
        }

        $form .= "</form>";

        return $form;
    }

    public function fields($order_id, $order_total, $callback_url)
    {
        $args = [
            'API_key' => $this->api_key,
            'website_index' => 1,
            'platform_order_id' => $order_id,
            'product_name' => '',
            'product_type' => 0, //1 : downloadable-virtual 0:real object,2:default
            'total_order_value' => $order_total,
            'currency' => $this->getCurrency(),
            'platform' => 0,
            'is_in_frame' => 0,
            'current_language' => $this->lang(),
            'modul_version' => $this->module_version,
            'random_nr' => rand(100000, 999999)
        ];

        $args = array_merge($args, $this->getBuyer()->generateArray());
        $args = array_merge($args, $this->getBillingAddress()->generateArray());
        $args = array_merge($args, $this->getShippingAdress()->generateArray());

        $data = $args["random_nr"] . $args["platform_order_id"] . $args["total_order_value"] . $args["currency"];
        $signature = hash_hmac('sha256', $data, $this->api_secret, true);
        $signature = base64_encode($signature);
        $args['signature'] = $signature;
        $args['callback'] = $callback_url;

        return $args;
    }

    //shopierden gelen dataları kontrol eder.
    public function verifyShopierSignature($post_data)
    {
        if (isset($post_data['platform_order_id'])) {
            $order_id = $post_data['platform_order_id'];
            $random_nr = $post_data['random_nr'];
            if ($order_id != '') {
                $signature = base64_decode($_POST["signature"]);
                $expected = hash_hmac('sha256', $random_nr . $order_id, $this->api_secret, true);

                if ($signature == $expected)
                    return true;

            }


        }
        return false;
    }

    private function getCurrency()
    {
        $currencyList = ['TRY' => 0, 'USD' => 1, 'EUR' => 2];
        return $currencyList[strtoupper($this->currency)] ?? 0;
    }

    private function lang()
    {
        $current_language = "tr-TR";
        $current_lan = 1;
        if ($current_language == "tr-TR") {
            $current_lan = 0;
        }

        return $current_lan;
    }

}
