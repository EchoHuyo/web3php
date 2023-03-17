<?php

namespace Web3php\Contract\Event;

use Web3php\Address\AddressInterface;
use Web3php\Contract\Event\Item\DecodeInputItem;
use Web3php\Contract\Event\Item\LogsItem;

interface DecodeEventInterface
{
    /**
     * @param LogsItem $logsItem
     * @return void
     */
    public function handle(LogsItem $logsItem): void;

    /**
     * @param array $topics
     * @param string $data
     * @param AddressInterface $contractAddress
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics, string $data, AddressInterface $contractAddress): DecodeInputItem;

}