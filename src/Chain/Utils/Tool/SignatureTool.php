<?php

namespace Web3php\Chain\Utils\Tool;

use Web3\Utils;

class SignatureTool
{
    public static function signatureCompare(string $signature, string $signatureCompare): bool
    {
        return self::formatSignature($signature) === self::formatSignature($signatureCompare);
    }

    public static function formatSignature(string $signature): string
    {
        return mb_strtolower(Utils::stripZero($signature));
    }
}