<?php

namespace Web3php\Contract\Event;

use Web3php\Contract\Event\Item\LogItem;

abstract class AbstractEventDecode implements DecodeEventInterface
{
    public function handle(LogItem $logItem): void
    {
        //todo
    }
}