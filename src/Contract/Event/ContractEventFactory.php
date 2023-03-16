<?php

namespace Web3php\Contract\Event;

use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Contract\Event\EventContract\EventContractInterface;
use Web3php\Contract\Event\EventSignature\EventSignatureInterface;

class ContractEventFactory
{
    /**
     * @param ChainInterface $chain
     * @param EventSignatureInterface $eventSignature
     * @param EventContractInterface|null $eventContract
     * @return ContractEvent
     */
    public function make(ChainInterface $chain, EventSignatureInterface $eventSignature, ?EventContractInterface $eventContract): ContractEvent
    {
        return new ContractEvent($chain, $eventSignature, $eventContract);
    }
}