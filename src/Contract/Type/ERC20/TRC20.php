<?php

namespace Web3php\Contract\Type\ERC20;

use phpseclib\Math\BigInteger;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Utils\Receiver;
use Web3php\Contract\TronContract;

class TRC20 extends TronContract implements IERC20Interface
{

    public function name(): string
    {
        return current($this->call()->name());
    }

    public function symbol(): string
    {
        return current($this->call()->symbol());
    }

    public function decimals(): int
    {
        $decimals = current($this->call()->decimals());
        return (int)$decimals->toString();
    }

    public function totalSupply(): string
    {
        $data = $this->call()->totalSupply();
        if ($data) {
            $data = current($data);
            return $this->fromWei($data);
        }
        return $data;
    }

    public function balanceOf(AddressInterface $address): string
    {
        $data = $this->call()->balanceOf($this->formatAddress($address));
        if ($data) {
            $data = current($data);
        }
        return $this->fromWei($data);
    }

    public function balanceOfByBlockNumber(AddressInterface $address, int $block = 0): string
    {
        $data = $this->call()->balanceOf($this->formatAddress($address), $block == 0 ? "latest" : $block);
        if ($data) {
            $data = current($data);
        }
        return $this->fromWei($data);
    }

    public function transfer(Receiver $receiver): string
    {
        $encode = [
            $this->formatAddress($receiver->address),
            $this->toWei($receiver->amount),
        ];
        return $this->send()->transfer(...$encode);
    }

    public function allowance(AddressInterface $address, AddressInterface $toAddress): string
    {
        $data = $this->call()->allowance($this->formatAddress($address), $this->formatAddress($toAddress));
        if ($data) {
            $data = current($data);
        }
        return $this->fromWei($data);
    }

    public function approve(Receiver $receiver): string
    {
        $encode = [
            $this->formatAddress($receiver->address),
            $this->toWei($receiver->amount),
        ];
        return $this->send()->approve(...$encode);
    }

    // 格式化金额
    public function fromWei(BigInteger $bigInteger): string
    {
        $decimals = $this->decimals();
        return $this->getChain()->fromWei($bigInteger, $decimals, $decimals);
    }

    // 格式化金额 对象
    public function toWei(string $amount): BigInteger
    {
        return $this->getChain()->toWei($amount, $this->decimals());
    }
}