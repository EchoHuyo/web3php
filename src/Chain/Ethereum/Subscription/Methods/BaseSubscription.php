<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

use Web3php\Chain\Utils\JsonRpc\AbstractJsonRpc;

class BaseSubscription extends AbstractJsonRpc
{
    protected string $method = "eth_subscribe";
}