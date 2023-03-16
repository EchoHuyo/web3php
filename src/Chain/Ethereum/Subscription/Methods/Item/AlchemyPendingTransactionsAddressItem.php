<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods\Item;

use Web3php\Address\Ethereum\EthereumAddress;

class AlchemyPendingTransactionsAddressItem implements ItemInterface
{
    /**
     * @param EthereumAddress[] $toAddresses
     * @param EthereumAddress[] $fromAddresses
     */
    public function __construct(protected array $toAddresses = [] ,protected array $fromAddresses = [])
    {

    }

    public function toArray(): ?array
    {
        $result = null;
        foreach ($this->toAddresses as $toAddress){
            $result["toAddress"][] = $toAddress->toString();
        }
        foreach ($this->fromAddresses as $fromAddress){
            $result["fromAddress"][] = $fromAddress->toString();
        }
        return $result;
    }
}