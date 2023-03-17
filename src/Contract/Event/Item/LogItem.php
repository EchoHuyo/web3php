<?php

namespace Web3php\Contract\Event\Item;

use Web3php\Chain\Utils\Tool\HexTool;

class LogItem
{
    public function __construct(
        public int              $logIndex,
        public string           $contractAddress,
        public string           $eventSignature,
        public array            $topics,
        public string           $data,
        public string           $hash,
        public int              $blockNumber,
        public int              $transactionIndex,
        public string           $blockHash,
        public ?DecodeInputItem $decodeInputItem
    )
    {

    }

    /**
     * @param array $log
     * {
     * ["address"]=> string(42)
     * ["topics"]=> string[] {
     *      [0]=> string(66)
     * }
     * ["data"]=> string
     * ["blockNumber"]=> string
     * ["transactionHash"] => string
     * ["transactionIndex"] => string
     * ["blockHash"]=> string(66)
     * ["logIndex"]=> string
     * ["removed"]=>bool
     * }
     * @return static
     */
    public static function created(array $log): static
    {
        return new LogItem(
            HexTool::hexToInt($log["logIndex"]),
            $log["address"],
            $log['topics'][0],
            $log['topics'],
            $log["data"],
            $log["transactionHash"],
            HexTool::hexToInt($log["blockNumber"]),
            HexTool::hexToInt($log["transactionIndex"]),
            $log["blockHash"],
            null
        );
    }
}