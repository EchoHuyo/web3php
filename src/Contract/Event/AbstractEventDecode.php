<?php

namespace Web3php\Contract\Event;

use Web3\Utils;
use Web3php\Contract\Event\Item\LogsItem;

abstract class AbstractEventDecode implements DecodeEventInterface
{
    public static function signatureCompare(string $signature, string $signatureCompare): bool
    {
        return self::formatSignature($signature) === self::formatSignature($signatureCompare);
    }

    public static function formatSignature(string $signature): string
    {
        return mb_strtolower(Utils::stripZero($signature));
    }

    public function huddle(LogsItem $logsItem): void
    {
        //todo
    }
}