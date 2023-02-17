<?php

namespace Web3php\Contract;


use Web3php\Contract\Call\ContractCallInterface;
use Web3php\Contract\Send\ContractSendInterface;

interface ContractInterface
{
    public function call(): ContractCallInterface;

    public function send(): ContractSendInterface;
}