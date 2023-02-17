<?php

namespace Web3php\Contract\Event;

use Web3php\Address\AddressInterface;
use Web3php\Contract\Event\Item\DecodeInputItem;

interface BaseEventInterface
{
    /**
     * @param string $hash
     * @param AddressInterface $contractAddress
     * @param string $eventName
     * @param int $logKey
     * @param array $data
     * @return void
     */
    public function huddle(string $hash, AddressInterface $contractAddress, string $eventName, int $logKey, array $data): void;

    /**
     * @param array $topics
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics): DecodeInputItem;
}