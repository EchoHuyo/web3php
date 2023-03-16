<?php

namespace Web3php\Address;

use Web3p\EthereumUtil\Util;
use Web3php\Address\Ethereum\EthereumAddress;
use Web3php\Address\Tool\AddressTool;
use Web3php\Address\Tron\TronAddress;
use Web3php\Chain\Utils\Sender;
use Web3php\Constants\Enums\Address\AddressType;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Constants\Errors\AddressErrors\ErrorCode;
use Web3php\Exception\AddressException;

class AddressFactory
{
    protected AddressTool $tool;

    public function __construct(protected Util $util, protected TronAddressUtil $tronUtil)
    {
        $this->tool = new AddressTool($this);
    }

    public function make(string $addressType, string $address): AddressInterface
    {
        return match ($addressType) {
            AddressType::TronAddress => new TronAddress($address),
            AddressType::EthereumAddress => new EthereumAddress($address),
            default => throw new AddressException(ErrorCode::NOT_FOUND_CHAIN),
        };
    }

    public function makeEthereumAddress(string $address): EthereumAddress
    {
        return new EthereumAddress($address);
    }

    public function makeTronAddress(string $address): TronAddress
    {
        if (str_starts_with($address, '41')) {
            $address = $this->tronUtil->hexString2Address($address);
        }
        return new TronAddress($address);
    }

    public function compare(AddressInterface $addressEntity, string $compareAddress): bool
    {
        if ($addressEntity instanceof EthereumAddress) {
            if (TronAddress::isAddress($compareAddress)) {
                $address = $this->address41To0x($this->tronUtil->address2HexString($compareAddress));
                $compareAddress = $this->makeEthereumAddress($address)->getAddress();
            }
            return $addressEntity->compare($compareAddress);
        }
        if ($addressEntity instanceof TronAddress) {
            $ethAddress = $this->address41To0x($compareAddress);
            if (EthereumAddress::isAddress($ethAddress)) {
                $compareAddress = $this->tronUtil->hexString2Address(str_replace('0x', '41', $ethAddress));
            }
            return $addressEntity->compare($compareAddress);
        }
        return false;
    }

    public function privateKeyToAddress(string $addressType, string $privateKey): AddressInterface
    {
        $publicKey = $this->util->privateKeyToPublicKey($privateKey);
        $address = $this->util->publicKeyToAddress($publicKey);
        return match ($addressType) {
            AddressType::TronAddress => new TronAddress($this->tronUtil->hexString2Address(str_replace('0x', '41', $address))),
            AddressType::EthereumAddress => new EthereumAddress($address),
            default => throw new AddressException(ErrorCode::NOT_FOUND_CHAIN),
        };
    }

    protected function address41To0x(string $address): string
    {
        if (str_starts_with($address, '41')) {
            $address = substr_replace($address, '0x', 0, 2);
        }
        return $address;
    }

    public function ethereumToTron(AddressInterface $address): AddressInterface
    {
        $address = $this->tronUtil->hexString2Address(str_replace('0x', '41', $address->getAddress()));
        return $this->makeTronAddress($address);
    }

    public function tronToEthereum(AddressInterface $address): AddressInterface
    {
        $address = $this->address41To0x($this->tronUtil->address2HexString($address->getAddress()));
        return $this->makeEthereumAddress($address);
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

    public function generateAddress(): Sender
    {
        return $this->tool->generate();
    }
}