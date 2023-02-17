<?php

declare(strict_types=1);

namespace Web3php\Contract\Call;


use Web3php\Contract\TronContract;

class TronContractCall implements ContractCallInterface
{
    public function __construct(protected TronContract $contract)
    {
    }

    public function __call(string $name, array $arguments)
    {
        $ownerAddress = $this->contract->getChain()->getSender()->address->getAddress();
        if (empty($ownerAddress)) {
            $ownerAddress = '410000000000000000000000000000000000000000';
        }
        $tron = $this->contract->getChain()->getTron();
        return $tron->getTransactionBuilder()
            ->triggerConstantContract(
                json_decode($this->contract->getConfig()->abi, true),
                $tron->address2HexString($this->contract->getContractAddress()),
                $name,
                $arguments,
                $tron->address2HexString($ownerAddress)
            );
    }
}
