<?php

namespace Web3php\Contract\Event;

use phpseclib\Math\BigInteger;
use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Contract\Event\EventContract\EventContractInterface;
use Web3php\Contract\Event\EventSignature\EventSignatureInterface;
use Web3php\Contract\Event\Item\LogsItem;

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
        protected ?EventContractInterface $eventContract
    )
    {

    }

    /**
     * @param string $hash
     * @return LogsItem[]|null
     */
    public function listener(string $hash): array|null
    {
        /**
         * @var LogsItem[] $logsItems
         */
        $logsItems = null;
        $logs = $this->chain->checkHashStatus($hash);
        foreach ($logs as $log) {
            if (!is_array($log)) {
                $log = (array)$log;
            }
            $contractAddress = $this->chain->getAddress($log['address']);
            $eventSignature = $log['topics'][0];
            $logsItem = new LogsItem(
                $this->hexToInt($log["logIndex"]),
                $contractAddress->getAddress(),
                $eventSignature,
                $log['topics'],
                $log["data"],
                $hash,
                $this->hexToInt($log["blockNumber"]),
                $this->hexToInt($log["transactionIndex"]),
                $log["blockHash"],
                null
            );
            if ($this->eventContract) {
                $eventContract = $this->eventContract->retrieveContractAddress($contractAddress);
                if ($eventContract) {
                    $logsItem = $this->huddle($eventContract, $logsItem);
                }
            }
            $signatureEvent = $this->eventSignature->retrieveEventSignature($eventSignature);
            if ($signatureEvent) {
                $logsItem = $this->huddle($signatureEvent, $logsItem);
            }
            $logsItems[] = $logsItem;
        }
        return $logsItems;
    }

    protected function huddle(DecodeEventInterface $contractHuddle, LogsItem $logsItem): LogsItem
    {
        $decodeInput = $contractHuddle->decodeEvent($logsItem->topics, $logsItem->data,
            $this->chain->getAddress($logsItem->contractAddress));
        $logsItem->decodeInputItem = $decodeInput;
        $contractHuddle->huddle($logsItem);
        return $logsItem;
    }

    protected function hexToInt(string $hex): int
    {
        return (int)(new BigInteger($hex, "16"))->toString();
    }
}