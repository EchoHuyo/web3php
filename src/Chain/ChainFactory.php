<?php

use Web3php\Address\AddressFactory;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Chain\Ethereum\Ethereum;
use Web3php\Chain\Tron\TronChain;

class ChainFactory
{
    public function __construct(protected AddressFactory $addressFactory)
    {

    }

    public function makeEthereum(ChainConfig $config):ChainInterface
    {
        return new Ethereum($config,$this->addressFactory);
    }

    public function makeTron(ChainConfig $config):ChainInterface
    {
        return new TronChain($config,$this->addressFactory);
    }
}