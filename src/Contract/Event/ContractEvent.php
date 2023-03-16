<?php

namespace Web3php\Contract\Event;

use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Chain\Utils\Tool\HexTool;
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
        protected ?EventContractInterface $eventContract = null
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
                HexTool::hexToInt($log["logIndex"]),
                $contractAddress->getAddress(),
                $eventSignature,
                $log['topics'],
                $log["data"],
                $hash,
                HexTool::hexToInt($log["blockNumber"]),
                HexTool::hexToInt($log["transactionIndex"]),
                $log["blockHash"],
                null
            );
            if ($this->eventContract) {
                $decodeEvent = $this->eventContract->retrieveContractAddress($contractAddress);
                if ($decodeEvent) {
                    $logsItem = $this->huddle($decodeEvent, $logsItem);
                }
            }
            $signatureDecodeEvent = $this->eventSignature->retrieveEventSignature($eventSignature);
            if ($signatureDecodeEvent) {
                $logsItem = $this->huddle($signatureDecodeEvent, $logsItem);
            }
            $logsItems[] = $logsItem;
        }
        return $logsItems;
    }

    protected function huddle(DecodeEventInterface $decodeEvent, LogsItem $logsItem): LogsItem
    {
        $decodeInput = $decodeEvent->decodeEvent(
            $logsItem->topics,
            $logsItem->data,
            $this->chain->getAddress($logsItem->contractAddress)
        );
        $logsItem->decodeInputItem = $decodeInput;
        $decodeEvent->huddle($logsItem);
        return $logsItem;
    }
}