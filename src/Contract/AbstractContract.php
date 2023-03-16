<?php

namespace Web3php\Contract;

use Web3php\Address\AddressInterface;
use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Contract\Call\ContractCallInterface;
use Web3php\Contract\Config\ContractConfig;
use Web3php\Contract\Send\ContractSendInterface;

abstract class AbstractContract implements ContractInterface
{
    protected ContractCallInterface $contractCall;

    protected ContractSendInterface $contractSend;

    protected AddressInterface $contractAddress;

    public function call(): ContractCallInterface
    {
        return $this->contractCall;
    }

    public function send(): ContractSendInterface
    {
        return $this->contractSend;
    }

    public function getContractAddress(): AddressInterface
    {
        return $this->contractAddress;
    }

    public abstract function getConfig(): ContractConfig;

    public abstract function setContractAddress(AddressInterface $address): void;

    public abstract function getChain(): ChainInterface;

    /**
     * @param string $type
     * @param string $paramName
     * @param mixed $param
     * @return mixed
     */
    protected function format(string $type, string $paramName, mixed $param): mixed
    {
        if ($type == 'address') {
            $param = $this->chain->getAddress($param);
        }
        if ($type == 'uint256') {
            $param = $param->toString();
        }
        return $param;
    }
}
