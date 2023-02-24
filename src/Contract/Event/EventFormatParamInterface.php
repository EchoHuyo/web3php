<?php

namespace Web3php\Contract\Event;

use Web3php\Address\AddressInterface;

interface EventFormatParamInterface
{

    /**
     * @param AddressInterface $address
     * @return void
     */
    public function setContractAddress(AddressInterface $address):void;

    /**
     * @param string $type
     * @param string $paramName
     * @param mixed $param
     * @return mixed
     */
    public function formatParam(string $type,string $paramName, mixed $param): mixed;
}