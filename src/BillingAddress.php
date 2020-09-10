<?php

namespace CanAvci\Shopier;

class BillingAddress extends Address implements GenerateArray
{
    public function generateArray(): array
    {
        return [
            'billing_address' => $this->getAddress(),
            'billing_city' => $this->getCity(),
            'billing_country' => $this->getCountry(),
            'billing_postcode' => $this->getPostcode()
        ];
    }
}