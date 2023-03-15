<?php

namespace Web3php\Address\Ethereum;

use Web3\Utils;
use Web3php\Address\AbstractAddress;
use Web3php\Constants\Errors\AddressErrors\ErrorCode;
use Web3php\Exception\AddressException;

class EthereumAddress extends AbstractAddress
{
    public function __construct(protected string $address)
    {
        if(!static::isAddress($this->address)){
            throw new AddressException(ErrorCode::ADDRESS_INVALID);
        }
        $this->address = Utils::toChecksumAddress($this->address);
    }

    public function compare(string $address): bool
    {
        if (Utils::isAddress($address)) {
            return Utils::toChecksumAddress($address) === Utils::toChecksumAddress($this->address);
        }
        return false;
    }

    public static function isAddress(string $address): bool
    {
        return Utils::isAddress($address);
    }

}