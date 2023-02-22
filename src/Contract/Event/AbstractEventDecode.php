<?php

namespace Web3php\Contract\Event;

use Web3\Utils;
use Web3php\Contract\Event\Item\LogsItem;

Abstract class AbstractEventDecode implements DecodeEventInterface
{
    public static function signatureCompare(string $signature,string $signatureCompare):bool
    {
        return self::formatSignature($signature) === self::formatSignature($signatureCompare);
    }

    public static function formatSignature(string $signature):string
    {
        return mb_strtolower(Utils::stripZero($signature));
    }

    public function huddle(LogsItem $logsItem): void
    {
       //todo
    }

    /**
     * @param string $type
     * @param string $paramName
     * @param mixed $param
     * @return mixed
     */
    public function formatParam(string $type,string $paramName, mixed $param): mixed
    {
        if ($type == 'uint256') {
            $param = $param->toString();
        }
        return $param;
    }
}