<?php

namespace Web3php\Contract\Event;

use Web3php\Address\AddressInterface;

interface ContractEventLoadInterface extends BaseEventInterface
{

    /**
     * @return AddressInterface
     */
    public function getContractAddress(): AddressInterface;
}