<?php

namespace Web3php\Chain\Config;

use Web3php\Chain\Utils\Sender;

class ChainConfig
{
    public function __construct(
        public string    $host,
        public int       $chainId = 0,
        public int|float $gasPrice = 0,
        public ?Sender   $sender = null
    )
    {

    }
}