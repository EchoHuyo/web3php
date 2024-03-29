<?php

namespace Web3php\Address\Tron;

use Web3php\Address\AbstractAddress;
use Web3php\Constants\Enums\Address\AddressCode;
use Web3php\Constants\Errors\AddressErrors\ErrorCode;
use Web3php\Exception\AddressException;
use Web3php\Address\Utils\TronAddressUtil;

class TronAddress extends AbstractAddress
{
    public function __construct(protected string $address)
    {
        if (!static::isAddress($this->address)) {
            throw new AddressException(ErrorCode::ADDRESS_INVALID);
        }
    }

    public function compare(string $address): bool
    {
        if (TronAddressUtil::isAddress($address)) {
            return strtoupper($this->address) === strtoupper($address);
        }
        return false;
    }

    public static function isAddress(string $address): bool
    {
        if (TronAddressUtil::isAddress($address) || $address == AddressCode::ZERO_ADDRESS) {
            return true;
        }
        return false;
    }
}