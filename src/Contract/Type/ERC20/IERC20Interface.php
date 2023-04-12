<?php

namespace Web3php\Contract\Type\ERC20;

use phpseclib\Math\BigInteger;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Utils\Receiver;

interface IERC20Interface
{
    public function name(): string;

    public function symbol(): string;

    public function decimals(): int;

    public function totalSupply(): string;

    public function balanceOf(AddressInterface $address): string;

    public function transfer(Receiver $receiver): string;

    public function allowance(AddressInterface $address, AddressInterface $toAddress): string;

    public function approve(Receiver $receiver): string;

    public function fromWei(BigInteger $bigInteger);

    public function toWei(string $amount): BigInteger;
}