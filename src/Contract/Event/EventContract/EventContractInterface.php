<?php

namespace Web3php\Contract\Event\EventContract;

use Web3php\Address\AddressInterface;
use Web3php\Contract\Event\DecodeEventInterface;

interface EventContractInterface
{
    /**
     * @param AddressInterface $address
     * @return DecodeEventInterface|null
     */
    public function retrieveContractAddress(AddressInterface $address):?DecodeEventInterface;
}