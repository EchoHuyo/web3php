<?php
namespace Web3php\Chain\Utils\Tool;

use phpseclib\Math\BigInteger;

class HexTool
{
    public static function hexToInt(string $hex): int
    {
        return (int)(new BigInteger($hex, "16"))->toString();
    }
}