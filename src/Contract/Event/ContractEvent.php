<?php

namespace Web3php\Contract\Event;

use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Constants\Errors\ChainErrors\ErrorCode;
use Web3php\Contract\Event\Item\LogsItem;
use Web3php\Exception\ChainException;

class ContractEvent
{
    /**
     * @var array<string,EventLoadInterface>
     */
    protected array $events = [];

    /**
     * @var array<string,ContractEventLoadInterface>
     */
    protected array $contractEvents = [];

    protected bool $isLoadEvent = false;

    protected bool $isLoadContractEvent = false;

    public function __construct(protected ChainInterface $chain)
    {

    }

    /**
     * @param EventLoadInterface[] $events
     * @return void
     */
    public function loadEvent(array $events): void
    {
        foreach ($events as $event) {
            $this->events[$event->getEventSignature()] = $event;
        }
        $this->isLoadEvent = true;
    }

    /**
     * @param ContractEventLoadInterface[] $events
     * @return void
     */
    public function loadContractEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->contractEvents[$event->getContractAddress()->getAddress()] = $event;
        }
        $this->isLoadContractEvent = true;
    }

    /**
     * @param string $hash
     * @param bool $isCheck
     * @return LogsItem[]|null
     */
    public function listener(string $hash, bool $isCheck = false): array|null
    {
        if ($isCheck && !$this->isLoadEvent && !$this->isLoadContractEvent) {
            throw new ChainException(ErrorCode::UNINITIALIZED_EVENT);
        }
        /**
         * @var LogsItem[] $logsItems
         */
        $logsItems = null;
        $logs = $this->chain->checkHashStatus($hash);
        foreach ($logs as $key => $log) {
            if (!is_array($log)) {
                $log = (array)$log;
            }
            $contractAddress = $this->chain->getAddress($log['address']);
            $eventSignature = $log['topics'][0];
            $logsItems[] = new LogsItem($key, $contractAddress, $eventSignature, $log['topics'], $log["data"], $hash);
            if ($this->isLoadContractEvent && isset($this->contractEvents[$contractAddress->getAddress()])) {
                /**
                 * @var ContractEventLoadInterface $contractHuddle
                 */
                $contractHuddle = $this->contractEvents[$contractAddress->getAddress()];
                $decodeInput = $contractHuddle->decodeEvent($log['topics']);
                $contractHuddle->huddle($hash, $contractAddress, $decodeInput->name, $key, $decodeInput->decodeDate);
            }
            if ($this->isLoadEvent && isset($this->events[$eventSignature])) {
                /**
                 * @var EventLoadInterface $contractHuddle
                 */
                $contractHuddle = $this->events[$eventSignature];
                $decodeInput = $contractHuddle->decodeEvent($log['topics']);
                $contractHuddle->huddle($hash, $contractAddress, $decodeInput->name, $key, $decodeInput->decodeDate);
            }
        }
        return $logsItems;
    }
}