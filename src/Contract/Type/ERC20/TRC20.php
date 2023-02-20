<?php

namespace Web3php\Contract\Type\ERC20;

use phpseclib\Math\BigInteger;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Utils\Receiver;
use Web3php\Contract\TronContract;

class TRC20 extends TronContract implements IERC20Interface
{
    /**
     * @var array<string,int>
     */
    protected array $decimals = [];

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
        $address = $this->getContractAddress()->getAddress();
        if (!isset($this->decimals[$address])) {
            $decimals = current($this->call()->decimals());
            $this->decimals[$address] = (int)$decimals->toString();
        }
        return $this->decimals[$address];
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
        $data = $this->call()->allowance($this->formatAddress($address), $toAddress->getAddress());
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