<?php

namespace Web3php\Contract;


use Web3php\Address\AddressInterface;
use Web3php\Address\Tron\TronAddress;
use Web3php\Chain\Tron\TronChain;
use Web3php\Contract\Call\TronContractCall;
use Web3php\Contract\Config\ContractConfig;
use Web3php\Contract\Send\TronContractSend;

class TronContract extends AbstractContract
{
    public function __construct(protected TronChain $chain, protected ContractConfig $config)
    {
        $this->setContractAddress($this->config->address);
    }

    public function getChain():TronChain
    {
        return $this->chain;
    }

    public function getConfig():ContractConfig
    {
        return $this->config;
    }

    public function setContractAddress(AddressInterface $address):void
    {
        $this->contractAddress = $address;
        $this->contractCall = new TronContractCall($this);
        $this->contractSend = new TronContractSend($this);
    }

    public function formatAddress(AddressInterface $address): string
    {
        $sendAddress = $address->getAddress();
        if ($address instanceof TronAddress) {
            $sendAddress = $this->chain->getTron()->address2HexString($address->getAddress());
        }
        return $sendAddress;
    }

}