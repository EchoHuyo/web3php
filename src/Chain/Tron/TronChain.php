<?php

namespace Web3php\Chain\Tron;

use IEXBase\TronAPI\Exception\TronException;
use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Tron;
use phpseclib\Math\BigInteger;
use Web3\Utils;
use Web3php\Address\AddressFactory;
use Web3php\Address\AddressInterface;
use Web3php\Chain\ChainInterface\ChainInterface;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Chain\Utils\Receiver;
use Web3php\Chain\Utils\Sender;
use Web3php\Constants\Errors\ChainErrors\ErrorCode;
use Web3php\Exception\ChainException;

class TronChain implements ChainInterface
{
    protected Sender $sender;

    protected Tron $tron;

    public function __construct(protected ChainConfig $config, protected AddressFactory $addressFactory)
    {
        if (!empty($this->config->sender)) {
            $this->setSender($this->config->sender);
        }
        $this->resetTron($config->host, '');
    }

    public function resetTron(string $url, string $port = '50051')
    {
        if ($port) {
            $url = "{$url}:{$port}";
        }
        try {
            $fullNode = new HttpProvider($url);
            $solidityNode = new HttpProvider($url);
            $eventServer = new HttpProvider($url);
            $this->tron = new Tron($fullNode, $solidityNode, $eventServer);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
    }

    public function getSender(): Sender
    {
        if (empty($this->sender)) {
            throw new ChainException(ErrorCode::SENDER_NOT_IMPLEMENTED);
        }
        return $this->sender;
    }

    public function getTron(): Tron
    {
        return $this->tron;
    }

    public function setSender(Sender $sender): void
    {
        $this->sender = $sender;
    }

    public function sendTransaction(Receiver $receiver, string $data = null): string
    {
        if (empty($this->sender)) {
            throw new ChainException(ErrorCode::SENDER_NOT_IMPLEMENTED);
        }
        $this->tron->setPrivateKey($this->sender->privateKey);
        try {
            $result = $this->tron->sendTransaction($receiver->address->getAddress(), (float)$receiver->amount, null, $this->sender->address->getAddress());
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        if ($result['result']) {
            return $result['txid'];
        }
        throw new ChainException(json_encode($result));
    }

    public function sendRawTransaction(string $hash): string
    {
        $hash = json_decode($hash, true);
        try {
            $result = $this->tron->sendRawTransaction($hash);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        if ($result['result']) {
            return $result['txid'];
        }
        throw new ChainException($this->tron->hexString2Utf8($result['message']));
    }

    public function getBalance(AddressInterface $address): BigInteger
    {
        try {
            $balance = $this->tron->getBalance($address->getAddress(), true);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        return new BigInteger((string)$balance);
    }

    public function getTransaction(string $hash): object
    {
        try {
            return (object)$this->tron->getTransaction($hash);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
    }

    public function getTransactionReceipt(string $hash): array
    {
        try {
            $data = $this->tron->getTransactionInfo($hash);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        return $data;
    }

    /**
     * 只判断合约交易是否成功
     * @param string $hash
     */
    public function checkHashStatus(string $hash)
    {
        try {
            $data = $this->tron->getTransactionInfo($hash);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        if (empty($data)) {
            throw new ChainException(ErrorCode::TRANSACTION_BEING_PACKAGED);
        }
        if ($data['receipt']['result'] != 'SUCCESS') {
            throw new ChainException(ErrorCode::TRANSACTION_FAILED . $this->tron->hexString2Utf8($data['contractResult'][0]));
        }
    }

    public function getBlock(): int
    {
        try {
            $block = $this->tron->getBlock();
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        return $block['block_header']['raw_data']['number'];
    }

    public function getBlockByNumber(int $number): array
    {
        try {
            $block = $this->tron->getBlock($number);
        } catch (TronException $e) {
            throw new ChainException($e->getMessage());
        }
        if (!empty($block['transactions'])) {
            foreach ($block['transactions'] as $transaction) {
                $block['transactions'][] = $transaction["txID"];
            }
        }
        return $block;
    }

    public function getAddress(string $address): AddressInterface
    {
        if (Utils::isZeroPrefixed($address)) {
            $address = $this->addressFactory->makeEthereumAddress($address);
            return $this->addressFactory->ethereumToTron($address);
        }
        return $this->addressFactory->makeTronAddress($address);
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