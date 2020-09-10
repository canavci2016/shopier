<?php

namespace CanAvci\Shopier;

class ShippingAddress extends Address implements GenerateArray
{
    public function generateArray(): array
    {
        return [
            'shipping_address' => $this->getAddress(),
            'shipping_city' => $this->getCity(),
            'shipping_country' => $this->getCountry(),
            'shipping_postcode' => $this->getPostcode(),
        ];
    }
}