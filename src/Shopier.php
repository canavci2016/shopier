<?php

namespace CanAvci\Shopier;

class Shopier
{
    private $payment_url = 'https://www.shopier.com/ShowProduct/api_pay4.php';
    private $api_key, $api_secret, $module_version, $buyer = [], $currency = 'TRY';
    private $billingAddress;
    private $shippingAdress;

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

    public function __construct($api_key, $api_secret, $module_version = ('1.0.4'))
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->module_version = $module_version;
    }

    public function setBuyer(array $fields = [])
    {
        $this->buyerValidateAndLoad($this->buyerFields(), $fields);
    }

    private function buyerValidateAndLoad($validationFields, $fields)
    {
        $diff = array_diff_key($validationFields, $fields);

        if (count($diff) > 0)
            throw new Exception(implode(',', array_keys($diff)) . ' fields are required');

        foreach ($validationFields as $key => $buyerField) {
            $this->buyer[$key] = $fields[$key];
        }
    }

    public function generateFormObject($order_id, $order_total, $callback_url)
    {

        $diff = array_diff_key($this->buyerFields(), $this->buyer);

        if (count($diff) > 0)
            throw new \Exception(implode(',', array_keys($diff)) . ' fields are required use "setBuyer()" method ');


        $billingAddress = $this->getBillingAddress();
        $shippingAddress = $this->getShippingAdress();

        $args = array(
            'API_key' => $this->api_key,
            'website_index' => 1,
            'platform_order_id' => $order_id,
            'product_name' => '',
            'product_type' => 0, //1 : downloadable-virtual 0:real object,2:default
            'buyer_name' => $this->buyer['first_name'],
            'buyer_surname' => $this->buyer['last_name'],
            'buyer_email' => $this->buyer['email'],
            'buyer_account_age' => 0,
            'buyer_id_nr' => $this->buyer['id'],
            'buyer_phone' => $this->buyer['phone'],
            'billing_address' => $billingAddress->getAddress(),
            'billing_city' => $billingAddress->getCity(),
            'billing_country' => $billingAddress->getCountry(),
            'billing_postcode' => $billingAddress->getPostcode(),
            'shipping_address' => $shippingAddress->getAddress(),
            'shipping_city' => $shippingAddress->getCity(),
            'shipping_country' => $shippingAddress->getCountry(),
            'shipping_postcode' => $shippingAddress->getPostcode(),
            'total_order_value' => $order_total,
            'currency' => $this->getCurrency(),
            'platform' => 0,
            'is_in_frame' => 0,
            'current_language' => $this->lang(),
            'modul_version' => $this->module_version,
            'random_nr' => rand(100000, 999999)
        );


        $data = $args["random_nr"] . $args["platform_order_id"] . $args["total_order_value"] . $args["currency"];
        $signature = hash_hmac('sha256', $data, $this->api_secret, true);
        $signature = base64_encode($signature);
        $args['signature'] = $signature;
        $args['callback'] = $callback_url;

        return [
            'elements' => [
                [
                    'tag' => 'form',
                    'attributes' => [
                        'id' => 'shopier_form_special',
                        'method' => 'post',
                        'action' => $this->payment_url
                    ],
                    'children' => array_map(function ($key, $value) {
                        return [
                            'tag' => 'input',
                            'attributes' => [
                                'name' => $key,
                                'value' => $value,
                                'type' => 'hidden',
                            ]
                        ];

                    }, array_keys($args), array_values($args))
                ]
            ]
        ];

    }


    public function generateForm($order_id, $order_total, $callback_url)
    {
        $obj = $this->generateFormObject($order_id, $order_total, $callback_url);

        return $this->recursiveHtmlStringGenerator($obj['elements']);
    }

    public function run($order_id, $order_total, $callback_url)
    {

        $form = $this->generateForm($order_id, $order_total, $callback_url);


        return '<!doctype html>
             <html lang="en">
            <head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
     </head>
' . $form . '
    <body>
        <script type="text/javascript">
					document.getElementById("shopier_form_special").submit();
		</script>
     </body>
    </html>
     ';
    }

    // generateFormObject() sınıfının verdiği formattaki arrayden structure çıkartan yapıdırı.
    private function recursiveHtmlStringGenerator(array $elements = [], $string = null)
    {
        foreach ($elements as $element) {
            $attributes = $element['attributes'] ?? [];
            $attributes = array_map(function ($key, $value) {
                return $key . '="' . $value . '"';
            }, array_keys($attributes), array_values($attributes));
            $attribute_string = implode(' ', $attributes);
            $html_in = $element['source'] ?? null;
            $string .= "<{$element['tag']} {$attribute_string} > " . $html_in;

            if (isset($element['children']) && is_array($element['children']))
                $string = $this->recursiveHtmlStringGenerator($element['children'], $string);

            $string .= "</{$element['tag']}>";

        }
        return $string;
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

    private function buyerFields()
    {
        return [
            'id' => true,
            'first_name' => true,
            'last_name' => true,
            'email' => true,
            'phone' => true,
        ];
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
