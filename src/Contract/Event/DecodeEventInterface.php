<?php

namespace Web3php\Contract\Event;

use Web3php\Contract\Event\Item\DecodeInputItem;
use Web3php\Contract\Event\Item\LogsItem;

interface DecodeEventInterface
{
    /**
     * @param LogsItem $logsItem
     * @return void
     */
    public function huddle(LogsItem $logsItem): void;

    /**
     * @param array $topics
     * @param string $data
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics,string $data): DecodeInputItem;

    /**
     * @param string $type
     * @param string $paramName
     * @param mixed $param
     * @return mixed
     */
    public function formatParam(string $type,string $paramName, mixed $param): mixed;
}