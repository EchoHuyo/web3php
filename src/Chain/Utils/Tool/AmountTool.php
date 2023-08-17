<?php

namespace Web3php\Chain\Utils\Tool;

use phpseclib\Math\BigInteger;

class AmountTool
{
    public function __construct(protected string|BigInteger $amount,protected int $decimals)
    {
        if($this->amount instanceof BigInteger){
            $this->amount = $this->amount->toString();
        }
    }
    public function fromWei(int $scale = 6): string
    {
        $amount = bcdiv($this->amount, bcpow('10', (string)$this->decimals), $scale);
        return preg_replace('/[.]$/', '', rtrim($amount, "0"));
    }

    public function toWei(): BigInteger
    {
        return new BigInteger(bcmul($this->amount, bcpow('10', (string)$this->decimals)));
    }
}