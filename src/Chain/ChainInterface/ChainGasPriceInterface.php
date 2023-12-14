<?php

namespace Web3php\Chain\ChainInterface;

interface ChainGasPriceInterface
{
    public function getGasPrice():string;
}