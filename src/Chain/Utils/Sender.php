<?php

namespace Web3php\Chain\Utils;



use Web3php\Address\AddressInterface;

class Sender
{
    public function __construct(public AddressInterface $address, public string $privateKey)
    {

    }
}