<?php

namespace Web3php\Address;

abstract class AbstractAddress implements AddressInterface
{
    protected string $address;

    public function getAddress(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->address;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}