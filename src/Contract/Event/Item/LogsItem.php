<?php

namespace Web3php\Contract\Event\Item;

use Web3php\Address\AddressInterface;

class LogsItem
{
    public function __construct(
        public int              $key,
        public AddressInterface $contractAddress,
        public string           $eventSignature,
        public array            $topics,
        public string           $data,
        public string           $hash
    )
    {

    }
}