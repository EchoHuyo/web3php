<?php

namespace Web3php\Contract\Event;

use Web3php\Contract\Event\Item\LogsItem;

abstract class AbstractEventDecode implements DecodeEventInterface
{
    public function handle(LogsItem $logsItem): void
    {
        //todo
    }
}