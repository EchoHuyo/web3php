<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

class NewPendingTransactions extends BaseSubscription
{

    public function __construct()
    {
        $arguments = ["newPendingTransactions"];
        parent::__construct($arguments);
    }
}