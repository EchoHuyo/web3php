<?php

namespace Web3php\Address\Helper;

use kornrunner\Keccak;
use Sop\CryptoEncoding\PEM;
use Sop\CryptoTypes\Asymmetric\EC\ECPrivateKey;
use Web3\Utils;
use Web3p\EthereumUtil\Util;
use Web3php\Address\AddressFactory;
use Web3php\Address\AddressInterface;
use Web3php\Address\Ethereum\EthereumAddress;
use Web3php\Address\Tron\TronAddress;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Chain\Utils\Sender;
use Web3php\Constants\Enums\Address\AddressType;
use Web3php\Constants\Errors\AddressErrors\ErrorCode;
use Web3php\Exception\AddressException;

class AddressHelper
{
    public function __construct(
        protected AddressFactory $addressFactory,
        protected Util $util,
        protected TronAddressUtil $tronUtil
    )
    {

    }

    protected function address41To0x(string $address): string
    {
        if (str_starts_with($address, '41')) {
            $address = substr_replace($address, '0x', 0, 2);
        }
        return $address;
    }

    public function compare(AddressInterface $addressEntity, string $compareAddress): bool
    {
        if ($addressEntity instanceof EthereumAddress) {
            if (TronAddress::isAddress($compareAddress)) {
                $address = $this->address41To0x($this->tronUtil->address2HexString($compareAddress));
                $compareAddress = $this->addressFactory->makeEthereumAddress($address)->toString();
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

    public function getAddressByChain(AddressInterface|string $address,string $chainType): AddressInterface
    {
        if(is_string($address)){
            $address = $this->addressFactory->make($address);
        }
        if($address instanceof EthereumAddress && $chainType == "TRON"){
            $address =  $this->ethereumToTron($address);
        }
        if($address instanceof EthereumAddress && $chainType == "ethereum"){
            $address =  $this->tronToEthereum($address);
        }
        return $address;
    }

    public function ethereumToTron(AddressInterface $address): AddressInterface
    {
        $address = $this->tronUtil->hexString2Address(str_replace('0x', '41', $address->toString()));
        return $this->addressFactory->makeTronAddress($address);
    }

    public function tronToEthereum(AddressInterface $address): AddressInterface
    {
        $address = $this->address41To0x($this->tronUtil->address2HexString($address->toString()));
        return $this->addressFactory->makeEthereumAddress($address);
    }

    /**
     * tron 只能验证 verifyMessageV2
     * @param string $address
     * @param string $msg
     * @param string $signed
     * @return bool
     */
    public function signVerify(string $address, string $msg, string $signed): bool
    {
        $address = $this->addressFactory->make($address);
        if($address instanceof EthereumAddress){
            $hash = $this->util->hashPersonalMessage($msg);
        }else{
            $hash = $this->tronUtil->hashPersonalMessage($msg);
        }
        $r = substr($signed, 2, 64);
        $s = substr($signed, 66, 64);
        $v = ord(hex2bin(substr($signed, 130, 2))) - 27;
        if ($v != ($v & 1)) {
            return false;
        }
        $publicKey = $this->util->recoverPublicKey($hash, $r, $s, $v);
        return $address->compare($this->util->publicKeyToAddress($publicKey));
    }

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
//        $keyDetail = openssl_pkey_get_details($result);
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
        return new Sender($this->addressFactory->makeEthereumAddress($address), $privateKeyHex);
    }

}