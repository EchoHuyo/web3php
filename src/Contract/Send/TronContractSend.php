<?php

namespace Web3php\Contract\Send;

use IEXBase\TronAPI\TRC20Contract;
use Web3php\Contract\TronContract;
use Web3php\Exception\ChainException;

class TronContractSend implements ContractSendInterface
{
    public function __construct(protected TronContract $contract)
    {
    }

    public function __call(string $name, array $arguments): string
    {
        $tron = $this->contract->getChain()->getTron();
        $owner = $this->contract->getChain()->getSender();
        $feeLimit = bcmul('1000', (string)TRC20Contract::TRX_TO_SUN);
        $transfer = $tron->getTransactionBuilder()
            ->triggerSmartContract(
                json_decode($this->contract->getConfig()->abi, true),
                $tron->address2HexString($this->contract->getContractAddress()->toString()),
                $name,
                $arguments,
                $feeLimit,
                $tron->address2HexString($owner->address->toString()),
            );

        $result = $tron->getTransactionInfo($transfer['txID']);
        if ($result) {
        }
        $tron->setPrivateKey($owner->privateKey);
        $tron->setAddress($owner->address->toString());
        $signedTransaction = $tron->signTransaction($transfer);
        $response = $tron->sendRawTransaction($signedTransaction);
        if (isset($response['result']) && $response['result']) {
            return $response['txid'];
        }
        throw new ChainException(json_encode($response));
    }
}
