<?php

namespace Web3php\Contract\Call;

use Web3php\Contract\EthereumContract;

class EthereumContractCall implements ContractCallInterface
{
    public function __construct(protected EthereumContract $contract)
    {
    }

    public function __call(string $name, array $arguments)
    {
        $data = null;
        array_unshift($arguments, $name);
        $arguments[] = function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        };
        var_dump("call --- ".$this->contract->getContract()->getToAddress());
        $this->contract->getContract()->call(...$arguments);
        return $data;
    }
}