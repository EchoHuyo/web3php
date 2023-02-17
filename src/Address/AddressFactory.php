<?php

namespace Web3php\Address;

use Web3\Utils;
use Web3p\EthereumUtil\Util;
use Web3php\Address\Ethereum\EthereumAddress;
use Web3php\Address\Tron\TronAddress;
use Web3php\Constants\Enums\Address\AddressType;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Constants\Errors\AddressErrors\ErrorCode;
use Web3php\Exception\AddressException;

class AddressFactory
{

    public function __construct(protected Util $util, protected TronAddressUtil $tronUtil)
    {

    }

    public function make(string $addressType, string $address): AddressInterface
    {
        return match ($addressType) {
            AddressType::TronAddress => new TronAddress($address),
            AddressType::EthereumAddress => new EthereumAddress($address),
            default => throw new AddressException(ErrorCode::NOT_FOUND_CHAIN),
        };
    }

    public function makeEthereumAddress(string $address): AddressInterface
    {
        return new EthereumAddress($address);
    }

    public function makeTronAddress(string $address): AddressInterface
    {
        return new TronAddress($address);
    }

    public function compare(AddressInterface $addressEntity, string $compareAddress): bool
    {
        if ($addressEntity instanceof EthereumAddress) {
            if (EthereumAddress::isAddress($compareAddress)) {
                return $addressEntity->compare($compareAddress);
            }
            if (TronAddress::isAddress($compareAddress)) {
                return strtoupper($compareAddress) === strtoupper($this->tronUtil->hexString2Address($addressEntity->getAddress()));
            }
        }
        if ($addressEntity instanceof TronAddress) {
            if (TronAddress::isAddress($compareAddress)) {
                return $addressEntity->compare($compareAddress);
            }
            $ethAddress = $this->address41To0x($compareAddress);
            if (EthereumAddress::isAddress($ethAddress)) {
                return strtoupper($addressEntity->getAddress()) === strtoupper($this->tronUtil->hexString2Address($compareAddress));
            }
        }
        return false;
    }

    public function privateKeyToAddress(string $addressType, string $privateKey): AddressInterface
    {
        $publicKey = $this->util->privateKeyToPublicKey($privateKey);
        $address = $this->util->publicKeyToAddress($publicKey);
        return match ($addressType) {
            AddressType::TronAddress => new TronAddress($this->tronUtil->hexString2Address($address)),
            AddressType::EthereumAddress => new EthereumAddress($address),
            default => throw new AddressException(ErrorCode::NOT_FOUND_CHAIN),
        };
    }

    protected function address41To0x(string $address): string
    {
        if (mb_substr($address, 0, 2) === '41') {
            $address = substr_replace($address, '0x', 0, 2);
        }
        return $address;
    }

    public function signVerify(string $address, string $msg, string $signed): bool
    {
        $hash = $this->util->hashPersonalMessage($msg);
        $r = substr($signed, 2, 64);
        $s = substr($signed, 66, 64);
        $v = ord(hex2bin(substr($signed, 130, 2))) - 27;
        if ($v != ($v & 1)) {
            return false;
        }
        $publicKey = $this->util->recoverPublicKey($hash, $r, $s, $v);
        return $this->makeEthereumAddress($address)->compare($this->util->publicKeyToAddress($publicKey));
    }
}