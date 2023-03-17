<?php

namespace Web3php\Contract\Type\ERC20And721Transfer;

use Web3php\Chain\Ethereum\Ethereum;
use Web3php\Constants\Enums\Address\AddressCode;
use Web3php\Contract\Config\ContractConfig;

class ERC20And721TransferEventContractFactory
{
    protected ERC20And721TransferEventContract $contract;

    public function make(Ethereum $chain): ERC20And721TransferEventContract
    {
        if (empty($this->contract)) {
            $this->contract = new ERC20And721TransferEventContract($chain,
                new ContractConfig($chain->getAddress(AddressCode::ZERO_ADDRESS), '[]')
            );
        }
        return $this->contract;
    }
}