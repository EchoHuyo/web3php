<?php

namespace Web3php\Contract\Config;


use Web3php\Address\AddressInterface;

class ContractConfig
{
    /**
     * @param AddressInterface $address
     * @param string $abi
     * @param string[] $event
     */
    public function __construct(public AddressInterface $address,
                                public string           $abi,
                                public array            $event = [])
    {

    }

}

