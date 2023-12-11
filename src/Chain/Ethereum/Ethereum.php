<?php

namespace Web3php\Chain\Ethereum;

use phpseclib\Math\BigInteger;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3php\Address\AddressFactory;
use Web3php\Address\AddressInterface;
use Web3php\Address\Ethereum\EthereumAddress;
use Web3php\Chain\AbstractChain;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Chain\Utils\Receiver;
use Web3php\Chain\Utils\Sender;
use Web3php\Chain\Utils\Tool\HexTool;
use Web3php\Constants\Errors\ChainErrors\ErrorCode;
use Web3php\Exception\ChainException;
use Web3p\EthereumTx\Transaction;

class Ethereum extends AbstractChain
{
    protected Sender $sender;

    protected Web3 $web3;

    public function __construct(protected ChainConfig $config, protected AddressFactory $addressFactory)
    {
        if (!empty($this->config->sender)) {
            $this->setSender($this->config->sender);
        }
    }

    public function getWeb3(): Web3
    {
        if (empty($this->web3)) {
            $host = $this->config->host;
            $timeout = 30;
            $requestManager = new HttpRequestManager($host, $timeout);
            $provider = new HttpProvider($requestManager);
            $this->web3 = new Web3($provider);
        }
        return $this->web3;
    }

    public function setSender(Sender $sender): void
    {
        $this->sender = $sender;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function sendTransaction(Receiver $receiver, string $data = null): string
    {
        if (empty($this->sender)) {
            throw new ChainException(ErrorCode::SENDER_NOT_IMPLEMENTED);
        }
        $value = "0x0";
        if ($receiver->mainAmount > 0) {
            $value = Utils::toHex(Utils::toWei($receiver->mainAmount, 'ether'));
        }
        // 获取 gasLimit
        $gasLimit = $this->getEstimateGas([
            'from' => $this->sender->address->toString(),
            'to' => $receiver->address->toString(),
            'value' => $value,
            'data' => $data ?? '0x0',
        ]);
        $tx = [
            'to' => $receiver->address->toString(),
            'value' => $value,
            'gas' => Utils::toHex($gasLimit, true),
            'gasPrice' => Utils::toHex($this->getGasPrice(), true),
            'nonce' => Utils::toHex($this->getNonce(), true),
            'chainId' => $this->config->chainId,
            'data' => $data ?? '0x0',
        ];
        $transaction = new Transaction($tx);
        $serializedTransaction = '0x' . $transaction->sign($this->sender->privateKey);
        return $this->sendRawTransaction($serializedTransaction);
    }

    protected function getGasPrice(): BigInteger
    {
        if ($this->config->gasPrice >= 0) {
            return Utils::toWei((string)$this->config->gasPrice, "gwei");
        }
        $data = null;
        $this->getWeb3()->getEth()->gasPrice(function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        });
        return $data;
    }

    public function retryTransactionByHash(string $hash): string
    {
        $transaction = $this->getTransaction($hash);
        if (!$this->sender->address->compare($transaction->from)) {
            throw new ChainException(ErrorCode::NOT_THE_SAME_FROM_ADDRESS);
        }
        $receiver = new Receiver(
            $this->getAddress($transaction->to),
            "0",
            $this->fromWei($transaction->value,18,18)
        );
        return $this->sendTransaction($receiver,$transaction->input);
    }

    public function sendRawTransaction(string $hash): string
    {
        $txid = '';
        $this->getWeb3()->getEth()->sendRawTransaction($hash, function ($error, $result) use (&$txid) {
            if ($error) {
                throw $error;
            }
            $txid = $result;
        });
        return $txid;
    }

    public function getBalance(AddressInterface $address): BigInteger
    {
        $data = null;
        $this->getWeb3()->getEth()->getBalance($address->toString(), function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        });
        return $data;
    }

    public function getTransaction(string $hash): object
    {
        $data = null;
        $this->getWeb3()->getEth()->getTransactionByHash($hash, function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        });
        return $data;
    }

    public function checkHashStatus(string $hash): void
    {
        $result = $this->getTransactionReceipt($hash);
        if (empty($result)) {
            throw new ChainException(ErrorCode::TRANSACTION_BEING_PACKAGED, ErrorCode::TRANSACTION_BEING_PACKAGED_CODE);
        }
        $status = HexTool::hexToInt($result->status);
        if ($status < 1) {
            throw new ChainException(ErrorCode::TRANSACTION_FAILED, ErrorCode::TRANSACTION_FAILED_CODE);
        }
    }

    public function getTransactionReceipt(string $hash): ?object
    {
        $data = null;
        $this->getWeb3()->getEth()->getTransactionReceipt($hash, function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        });
        return $data;
    }

    public function getBlock(): int
    {
        $data = null;
        $this->getWeb3()->getEth()->blockNumber(function ($error, $result) use (&$data) {
            $data = (int)$result->toString();
        });
        return $data;
    }

    public function getBlockByNumber(int $number): array
    {
        $data = null;
        $number = Utils::toHex(Utils::toWei((string)$number, 'wei'), true);
        $this->getWeb3()->getEth()->getBlockByNumber($number, false, function ($error, $result) use (&$data) {
            $data = $result;
        });
        return (array)$data;
    }

    public function getAddress(string $address): EthereumAddress
    {
        return $this->addressFactory->makeEthereumAddress($address);
    }

    // 一次交易中gas的可用上限
    protected function getEstimateGas(array $params): BigInteger
    {
        /**
         * @var BigInteger $gasLimit
         */
        $gasLimit = null;
        $this->getWeb3()->getEth()->estimateGas($params, function ($error, $result) use (&$gasLimit) {
            if ($error) {
                throw $error;
            }
            $gasLimit = $result;
        });
        return $gasLimit->add(new BigInteger("10000"));
    }

    // 获取nonce
    protected function getNonce(): BigInteger
    {
        $address = $this->sender->address->toString();
        $data = null;
        $this->getWeb3()->getEth()->getTransactionCount($address, 'pending', function ($error, $result) use (&$data) {
            if (!empty($error)) {
                throw $error;
            }
            $data = $result;
        });
        return $data;
    }

    public function getCode(EthereumAddress $address): string
    {
        $result = "";
        $this->getWeb3()->getEth()->getCode($address->toString(), function ($error, $data) use (&$result) {
            if (!empty($error)) {
                throw $error;
            }
            $result = $data;
        });
        return $result;
    }
}