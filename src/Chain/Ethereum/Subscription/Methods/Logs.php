<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

use Web3php\Chain\Ethereum\Subscription\Methods\Item\LogsItem;

class Logs extends BaseSubscription
{
    public function __construct(protected LogsItem $logsItem)
    {
        $arguments = ["logs"];
        $arguments[] = $this->logsItem->toArray();
        parent::__construct($arguments);
    }
}