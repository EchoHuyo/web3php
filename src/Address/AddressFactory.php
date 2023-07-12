<?php

namespace Web3php\Address;
use Web3\Utils;
use Web3php\Address\Ethereum\EthereumAddress;
use Web3php\Address\Tron\TronAddress;
use Web3php\Address\Utils\TronAddressUtil;

class AddressFactory
{
    protected TronAddressUtil $tronUtil;

    /**
     * @return TronAddressUtil
     */
    protected function getTronUtil():TronAddressUtil
    {
        if(empty($this->tronUtil)){
            $this->tronUtil = new TronAddressUtil();
        }
        return $this->tronUtil;
    }

    public function make(string $address): AddressInterface
    {
        if(Utils::isAddress($address)){
            return $this->makeEthereumAddress($address);
        }else{
            return $this->makeTronAddress($address);
        }
    }

    public function makeEthereumAddress(string $address): EthereumAddress
    {
        return new EthereumAddress($address);
    }

    public function makeTronAddress(string $address): TronAddress
    {
        if (str_starts_with($address, '41')) {
            $address = $this->getTronUtil()->hexString2Address($address);
        }
        return new TronAddress($address);
    }

    /**
     * @param AddressInterface $addressEntity
     * @param string $compareAddress
     * @return bool
     * @deprecated use AddressHelper()->compare()
     */
    public function compare(AddressInterface $addressEntity, string $compareAddress): bool
    {
        if ($addressEntity instanceof EthereumAddress) {
            if (TronAddress::isAddress($compareAddress)) {
                $address = $this->address41To0x($this->getTronUtil()->address2HexString($compareAddress));
                $compareAddress = $this->makeEthereumAddress($address)->toString();
            }
            return $addressEntity->compare($compareAddress);
        }
        if ($addressEntity instanceof TronAddress) {
            $ethAddress = $this->address41To0x($compareAddress);
            if (EthereumAddress::isAddress($ethAddress)) {
                $compareAddress = $this->getTronUtil()->hexString2Address(str_replace('0x', '41', $ethAddress));
            }
            return $addressEntity->compare($compareAddress);
        }
        return false;
    }

    protected function address41To0x(string $address): string
    {
        if (str_starts_with($address, '41')) {
            $address = substr_replace($address, '0x', 0, 2);
        }
        return $address;
    }
}