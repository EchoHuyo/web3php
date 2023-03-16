<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods;

use Web3php\Chain\Ethereum\Subscription\Methods\Item\AlchemyMinedTransactionsAddressItem;

class AlchemyMinedTransactions extends BaseSubscription
{
    /**
     * @param AlchemyMinedTransactionsAddressItem[]|null $addressItems
     * @param bool $includeRemoved
     * @param bool $hashesOnly
     */
    public function __construct(protected ?array $addressItems = null, protected bool $includeRemoved = false, protected bool $hashesOnly = false)
    {
        $arguments = ["alchemy_minedTransactions"];
        $params = [];
        foreach ($this->addressItems as $item) {
            $params["address"][] = $item->toArray();
        }
        $params["includeRemoved"] = $this->includeRemoved;
        $params["hashesOnly"] = $this->hashesOnly;
        if ($params) {
            $arguments[] = $params;
        }
        parent::__construct($arguments);
    }
}