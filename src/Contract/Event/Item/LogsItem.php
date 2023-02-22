<?php

namespace Web3php\Contract\Event\Item;

class LogsItem
{
    public function __construct(
        public int             $logIndex,
        public string          $contractAddress,
        public string          $eventSignature,
        public array           $topics,
        public string          $data,
        public string          $hash,
        public int             $blockNumber,
        public int             $transactionIndex,
        public string          $blockHash,
        public ?DecodeInputItem $decodeInputItem
    )
    {

    }
}