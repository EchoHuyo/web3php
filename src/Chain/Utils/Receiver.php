<?php

namespace Web3php\Chain\Utils;

use Web3php\Address\AddressInterface;

class Receiver
{
    public function __construct(public AddressInterface $address, public string $amount = "0", public string $mainAmount = "0")
    {

    }
}




