<?php

namespace Web3php\Contract\Event;

use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Chain\Utils\Tool\HexTool;
use Web3php\Contract\Event\EventContract\EventContractInterface;
use Web3php\Contract\Event\EventSignature\EventSignatureInterface;
use Web3php\Contract\Event\Item\DecodeInputItem;
use Web3php\Contract\Event\Item\LogItem;

class ContractEvent
{
    /**
     * @param ChainInterface $chain
     * @param EventSignatureInterface $eventSignature
     * @param EventContractInterface|null $eventContract
     */
    public function __construct(
        protected ChainInterface          $chain,
        protected EventSignatureInterface $eventSignature,
        protected ?EventContractInterface $eventContract = null
    )
    {

    }

    /**
     * @param LogItem $logItem
     * @param bool $callHandle
     * @return DecodeInputItem|null
     *
     */
    public function decodeTopic(LogItem $logItem, bool $callHandle = false): ?DecodeInputItem
    {
        $contractAddress = $this->chain->getAddress($logItem->contractAddress);
        $eventSignature = $logItem->eventSignature;
        $decodeInput = null;
        if ($this->eventContract) {
            $decodeEvent = $this->eventContract->retrieveContractAddress($contractAddress);
            if ($decodeEvent) {
                $decodeInput = $this->handle($decodeEvent, $logItem,$callHandle);
            }
        }
        $signatureDecodeEvent = $this->eventSignature->retrieveEventSignature($eventSignature);
        if ($signatureDecodeEvent) {
            $decodeInput = $this->handle($signatureDecodeEvent, $logItem,$callHandle);
        }
        return $decodeInput;
    }

    protected function handle(DecodeEventInterface $decodeEvent, LogItem $logItem, bool $callHandle): DecodeInputItem
    {
        $decodeInput = $decodeEvent->decodeEvent(
            $logItem->topics,
            $logItem->data,
            $this->chain->getAddress($logItem->contractAddress)
        );
        $logItem->decodeInputItem = $decodeInput;
        if($callHandle){
            $decodeEvent->handle($logItem);
        }
        return $decodeInput;
    }
}