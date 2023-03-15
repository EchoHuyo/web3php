<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods\Item;

use Web3php\Address\Ethereum\EthereumAddress;

class AlchemyPendingTransactionsAddressItem implements ItemInterface
{
    /**
     * @param EthereumAddress[]|null $toAddresses
     * @param EthereumAddress[]|null $fromAddresses
     */
    public function __construct(protected ?array $toAddresses = null ,protected ?array $fromAddresses = null)
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