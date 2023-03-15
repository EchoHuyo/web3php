<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods\Item;

use Web3php\Address\Ethereum\EthereumAddress;

class AlchemyMinedTransactionsAddressItem implements ItemInterface
{
    public function __construct(protected ?EthereumAddress $to = null,protected ?EthereumAddress $from = null)
    {

    }

    /**
     * @return array|null
     */
    public function toArray():?array
    {
        $result = null;
        if($this->to){
           $result["to"] = $this->to->toString();
        }
        if($this->from){
            $result["from"] = $this->from->toString();
        }
        return $result;
    }
}