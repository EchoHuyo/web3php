<?php

namespace Web3php\Contract\Type\ERC20;

use phpseclib\Math\BigInteger;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Utils\Receiver;
use Web3php\Contract\EthereumContract;

class ERC20 extends EthereumContract implements IERC20Interface
{
    protected int $decimals = 0;

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
        if ($this->decimals == 0) {
            $decimals = current($this->call()->decimals());
            $this->decimals = (int)$decimals->toString();
        }
        return $this->decimals;
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
        $data = $this->call()->balanceOf($address->getAddress());
        if ($data) {
            $data = current($data);
        }
        return $this->fromWei($data);
    }

    public function balanceOfByBlockNumber(AddressInterface $address, int $block = 0): string
    {
        $data = $this->call()->balanceOf($address->getAddress(), $block == 0 ? "latest" : $block);
        if ($data) {
            $data = current($data);
        }
        return $this->fromWei($data);
    }

    public function transfer(Receiver $receiver): string
    {
        $encode = [
            $receiver->address->getAddress(),
            $this->toWei($receiver->amount),
        ];
        return $this->send()->transfer(...$encode);
    }

    public function allowance(AddressInterface $address, AddressInterface $toAddress): string
    {
        $data = $this->call()->allowance($address->getAddress(), $toAddress->getAddress());
        if ($data) {
            $data = current($data);
        }
        return $this->fromWei($data);
    }

    public function approve(Receiver $receiver): string
    {
        $encode = [
            $receiver->address->getAddress(),
            $this->toWei($receiver->amount),
        ];
        return $this->send()->approve(...$encode);
    }

    // 格式化金额
    public function fromWei(BigInteger $bigInteger, int $scale = 6): string
    {
        return $this->getChain()->fromWei($bigInteger, $this->decimals(), $scale);
    }

    // 格式化金额 对象
    public function toWei(string $amount): BigInteger
    {
        return $this->getChain()->toWei($amount, $this->decimals());
    }
}