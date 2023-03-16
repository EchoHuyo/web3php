<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

use Web3php\Chain\Utils\JsonRpc\AbstractJsonRpc;

class Unsubscribe extends AbstractJsonRpc
{
    protected string $method = "eth_unsubscribe";

    public function __construct(string $subscribe)
    {
        $arguments[] = $subscribe;
        parent::__construct($arguments);
    }
}