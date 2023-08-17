<?php

namespace Web3php\Chain;

use phpseclib\Math\BigInteger;
use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Chain\Utils\Tool\AmountTool;

abstract class AbstractChain implements ChainInterface
{
    public function fromWei(BigInteger $bigInteger, int $decimals = 18, int $scale = 6): string
    {
        return (new AmountTool($bigInteger,$decimals))->fromWei($scale);
    }

    public function toWei(string $amount, int $decimals = 0): BigInteger
    {
        return (new AmountTool($amount,$decimals))->toWei();
    }
}