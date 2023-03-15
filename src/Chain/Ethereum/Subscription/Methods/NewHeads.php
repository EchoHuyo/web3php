<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

class NewHeads extends BaseSubscription
{
    public function __construct()
    {
        $arguments = ["newHeads"];
        parent::__construct($arguments);
    }
}