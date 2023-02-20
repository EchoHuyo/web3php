<?php

namespace Web3php\Chain\ChainInterface;


use phpseclib\Math\BigInteger;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Chain\Utils\Receiver;
use Web3php\Chain\Utils\Sender;

interface ChainInterface
{
    // 设置发送者
    public function setSender(Sender $sender): void;

    // 加密 交易 (后台自己做签名， 广播)
    public function sendTransaction(Receiver $receiver, string $data = null): string;

    // 交易（前端签名好了，直接广播）
    public function sendRawTransaction(string $hash): string;

    // 获取余额
    public function getBalance(AddressInterface $address): BigInteger;

    // 获取交易信息
    public function getTransaction(string $hash): object;

    // 检查交易 是否成功
    public function checkHashStatus(string $hash): array;

    public function getTransactionReceipt(string $hash):mixed;

    //获取当前区块高度
    public function getBlock(): int;

    public function getSender(): Sender;

    //获取区块信息
    public function getBlockByNumber(int $number): array;

    //获取链地址
    public function getAddress(string $address):AddressInterface;

    // 格式化金额
    public function fromWei(BigInteger $bigInteger, int $decimals = 0, int $scale = 6): string;

    // 格式化金额 对象
    public function toWei(string $amount, int $decimals = 0): BigInteger;

}