<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods\Item;

use Web3php\Address\Ethereum\EthereumAddress;

class LogsItem implements ItemInterface
{
    /**
     * @param string[] | array<array<string[]>> $topics
     * []: Any topics allowed.
     * [A]: A in first position (and anything after).
     * [null, B]: Anything in first position and B in second position (and anything after).
     * [A, B]: A in first position and B in second position (and anything after).
     * [[A, B], [A, B]]: (A or B) in first position and (A or B) in second position (and anything after).
     * @param EthereumAddress[]|null $addresses
     * Singular address or array of addresses. Only logs created from one of these addresses will be emitted.
     */
    public function __construct(protected array $topics, protected ?array $addresses = null)
    {

    }

    public function toArray(): ?array
    {
        $result = null;
        foreach ($this->addresses as $address){
            $result["address"][] = $address->toString();
        }
        $result["topics"] = [];
        if ($this->topics) {
            $result["topics"] = $this->topics;
        }
        return $result;
    }
}