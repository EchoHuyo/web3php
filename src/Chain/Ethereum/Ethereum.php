<?php

namespace Web3php\Chain\Ethereum;

use phpseclib\Math\BigInteger;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3php\Address\AddressFactory;
use Web3php\Address\AddressInterface;
use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Chain\Utils\Receiver;
use Web3php\Chain\Utils\Sender;
use Web3php\Constants\Errors\ChainErrors\ErrorCode;
use Web3php\Exception\ChainException;
use Web3p\EthereumTx\Transaction;

class Ethereum implements ChainInterface
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
        // 获取 gas上线
        $gasLimit = $this->getEstimateGas([
            'from' => $this->sender->address->getAddress(),
            'to' => $receiver->address->getAddress(),
            'value' => $value,
            'data' => $data ?? '0x',
        ]);
        // 获取 nonce
        $nonce = $this->getNonce();
        $tx = [
            'to' => $receiver->address->getAddress(),
            'value' => $value,
            'gas' => Utils::toHex(Utils::toWei($gasLimit->add(new BigInteger("10000")), 'wei'), true),
            'gasPrice' => Utils::toHex(Utils::toWei($this->getGasPrice()->toHex(), 'gwei'), true),
            'nonce' => Utils::toHex(Utils::toWei(new BigInteger($nonce), 'wei'), true),
            'chainId' => $this->config->chainId,
            'data' => $data ?? '0x',
        ];
        $transaction = new Transaction($tx);
        $serializedTransaction = '0x' . $transaction->sign($this->sender->privateKey);
        return $this->sendRawTransaction($serializedTransaction);
    }

    protected function getGasPrice(): BigInteger
    {
        if ($this->config->gasPrice >= 0) {
            return new BigInteger($this->config->gasPrice);
        }
        $data = null;
        $this->getWeb3()->getEth()->gasPrice(function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        });
        return new BigInteger($this->fromWei($data, 9));
    }

    public function retryTransactionByHash(string $hash): string
    {
        $transaction = $this->getTransaction($hash);
        if (!$this->sender->address->compare($transaction->from)) {
            throw new ChainException(ErrorCode::NOT_THE_SAME_FROM_ADDRESS);
        }
        // 获取 gas上线
        $gasLimit = $this->getEstimateGas([
            'from' => $transaction->from,
            'to' => $transaction->to,
            'value' => $transaction->value,
            'data' => $transaction->input,
        ]);
        // 获取 nonce
        $nonce = $this->getNonce();
        $tx = [
            'to' => $transaction->to,
            'value' => $transaction->value,
            'gas' => Utils::toHex(Utils::toWei($gasLimit->add(new BigInteger("10000")), 'wei'), true),
            'gasPrice' => Utils::toHex(Utils::toWei($this->getGasPrice()->toHex(), 'gwei'), true),
            'nonce' => Utils::toHex(Utils::toWei(new BigInteger($nonce), 'wei'), true),
            'chainId' => $this->config->chainId,
            'data' => $transaction->input,
        ];
        $transaction = new Transaction($tx);
        $serializedTransaction = '0x' . $transaction->sign($this->sender->privateKey);
        return $this->sendRawTransaction($serializedTransaction);
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
        $this->getWeb3()->getEth()->getBalance($address->getAddress(), function ($error, $result) use (&$data) {
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

    public function checkHashStatus(string $hash): array
    {
        $data = null;
        $this->getWeb3()->getEth()->getTransactionReceipt($hash, function ($error, $result) use (&$data) {
            if ($error) {
                throw $error;
            }
            $data = $result;
        });
        if (empty($data)) {
            throw new ChainException(ErrorCode::TRANSACTION_BEING_PACKAGED);
        }
        $status = Utils::toWei($data->status, 'wei');
        $check = (bool)$status->toString();
        if (!$check) {
            throw new ChainException(ErrorCode::TRANSACTION_FAILED);
        }
        return $data->logs;
    }

    public function getTransactionReceipt(string $hash): object
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

    public function getAddress(string $address): AddressInterface
    {
        return $this->addressFactory->makeEthereumAddress($address);
    }

    // 一次交易中gas的可用上限
    protected function getEstimateGas(array $params): BigInteger
    {
        $gasLimit = null;
        $this->getWeb3()->getEth()->estimateGas($params, function ($error, $result) use (&$gasLimit) {
            if ($error) {
                throw $error;
            }
            $gasLimit = $result;
        });
        return $gasLimit;
    }

    // 获取nonce
    protected function getNonce(): string
    {
        $data = null;
        $this->getWeb3()->getEth()->getTransactionCount($this->sender->address->getAddress(), function ($error, $result) use (&$data) {
            if (!empty($error)) {
                throw $error;
            }
            $data = $result;
        });
        return $data->toString();
    }

    public function fromWei(BigInteger $bigInteger, int $decimals = 18, int $scale = 6): string
    {
        $amount = bcdiv($bigInteger->toString(), bcpow('10', (string)$decimals), $scale);
        return preg_replace('/[.]$/', '', preg_replace('/0+$/', '', $amount));
    }

    public function toWei(string $amount, int $decimals = 0): BigInteger
    {
        return new BigInteger(bcmul($amount, bcpow('10', (string)$decimals)));
    }
}