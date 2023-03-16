<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

use Web3php\Chain\Ethereum\Subscription\Methods\Item\AlchemyPendingTransactionsAddressItem;

class AlchemyPendingTransactions extends BaseSubscription
{
    /**
     * @param AlchemyPendingTransactionsAddressItem|null $addressItem
     * @param bool $hashesOnly
     */
    public function __construct(protected ?AlchemyPendingTransactionsAddressItem $addressItem = null, protected bool $hashesOnly = false)
    {
        $arguments = ["alchemy_pendingTransactions"];
        $params = [];
        if ($this->addressItem) {
            $params = $this->addressItem->toArray();
        }
        $params["hashesOnly"] = $this->hashesOnly;
        $arguments[] = $params;
        parent::__construct($arguments);
    }
}