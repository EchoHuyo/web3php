<?php

namespace Web3php\Contract\Event;

use Web3php\Chain\ChainInterface\ChainInterface;

class ContractEventFactory
{
    /**
     * @var array<string, ContractEvent>
     */
    public array $contractEvents;

    public function __construct()
    {

    }

    /**
     * @param ChainInterface $chain
     * @param string $name
     * @return ContractEvent
     */
    public function make(ChainInterface $chain, string $name): ContractEvent
    {
        if (empty($this->contractEvents[$name])) {
            $this->contractEvents[$name] = new ContractEvent($chain);
        }
        return $this->contractEvents[$name];
    }
}