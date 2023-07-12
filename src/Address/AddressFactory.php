<?php

namespace Web3php\Address;

use kornrunner\Keccak;
use Sop\CryptoEncoding\PEM;
use Sop\CryptoTypes\Asymmetric\EC\ECPrivateKey;
use Web3\Utils;
use Web3p\EthereumUtil\Util;
use Web3php\Address\Ethereum\EthereumAddress;
use Web3php\Address\Tron\TronAddress;
use Web3php\Chain\Utils\Sender;
use Web3php\Constants\Enums\Address\AddressType;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Constants\Errors\AddressErrors\ErrorCode;
use Web3php\Exception\AddressException;

class AddressFactory
{
    protected Util $util;

    protected TronAddressUtil $tronUtil;

    /**
     * @return Util
     * @deprecated
     */
    protected function getUtil():Util
    {
        if(empty($this->util)){
            $this->util = new Util();
        }
        return $this->util;
    }

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

    /**
     * @param string $addressType
     * @param string $privateKey
     * @return AddressInterface
     * @deprecated  use AddressHelper()->privateKeyToAddress()
     */
    public function privateKeyToAddress(string $addressType, string $privateKey): AddressInterface
    {
        $publicKey = $this->getUtil()->privateKeyToPublicKey($privateKey);
        $address = $this->getUtil()->publicKeyToAddress($publicKey);
        return match ($addressType) {
            AddressType::TronAddress => new TronAddress($this->getTronUtil()->hexString2Address(str_replace('0x', '41', $address))),
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

    /**
     * @param AddressInterface $address
     * @return AddressInterface
     * @deprecated  use AddressHelper()->ethereumToTron()
     */
    public function ethereumToTron(AddressInterface $address): AddressInterface
    {
        $address = $this->getTronUtil()->hexString2Address(str_replace('0x', '41', $address->toString()));
        return $this->makeTronAddress($address);
    }

    /**
     * @param AddressInterface $address
     * @return AddressInterface
     * @deprecated  use AddressHelper()->tronToEthereum()
     */
    public function tronToEthereum(AddressInterface $address): AddressInterface
    {
        $address = $this->address41To0x($this->getTronUtil()->address2HexString($address->toString()));
        return $this->makeEthereumAddress($address);
    }

    /**
     * tron 只能验证 verifyMessageV2
     * @param string $address
     * @param string $msg
     * @param string $signed
     * @return bool
     * @deprecated  use AddressHelper()->signVerify()
     */
    public function signVerify(string $address, string $msg, string $signed): bool
    {
        if(!Utils::isAddress($address)){
            $address = $this->tronToEthereum($this->makeTronAddress($address));
            $hash = $this->getTronUtil()->hashPersonalMessage($msg);
        }else{
            $address = $this->makeEthereumAddress($address);
            $hash = $this->getUtil()->hashPersonalMessage($msg);
        }
        $r = substr($signed, 2, 64);
        $s = substr($signed, 66, 64);
        $v = ord(hex2bin(substr($signed, 130, 2))) - 27;
        if ($v != ($v & 1)) {
            return false;
        }
        $publicKey = $this->getUtil()->recoverPublicKey($hash, $r, $s, $v);
        return $address->compare($this->getUtil()->publicKeyToAddress($publicKey));
    }

    /**
     * @return Sender
     * @deprecated  use AddressHelper()->generateAddress()
     */
    public function generateAddress(): Sender
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1'
        ];
        $result = openssl_pkey_new($config);
        if (empty($result)) {
            throw new AddressException('ERROR: Fail to generate private key. -> ' . openssl_error_string());
        }
        openssl_pkey_export($result, $privateKey);
        $privatePem = PEM::fromString($privateKey);
        $ecPrivateKey = ECPrivateKey::fromPEM($privatePem);
        $ecPrivateSeq = $ecPrivateKey->toASN1();
        $privateKeyHex = bin2hex($ecPrivateSeq->at(1)->asOctetString()->string());
        $publicKeyHex = bin2hex($ecPrivateSeq->at(3)->asTagged()->asExplicit()->asBitString()->string());
        $publicKeyHex2 = substr($publicKeyHex, 2);
        try {
            $hash = Keccak::hash(hex2bin($publicKeyHex2), 256);
        } catch (\Exception $exception) {
            throw new AddressException($exception->getMessage());
        }
        $address = "0x" . substr($hash, -40);
        return new Sender($this->makeEthereumAddress($address), $privateKeyHex);
    }
}