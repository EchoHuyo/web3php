<?php

namespace Web3php\Address\Tool;


use kornrunner\Keccak;
use Sop\CryptoEncoding\PEM;
use Sop\CryptoTypes\Asymmetric\EC\ECPrivateKey;
use Web3php\Address\AddressFactory;
use Web3php\Chain\Utils\Sender;
use Web3php\Exception\AddressException;

class AddressTool
{
    public function __construct(protected AddressFactory $addressFactory){

    }

    public function generate():Sender
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1'
        ];
        $result = openssl_pkey_new($config);
        if(empty($result)){
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
        $address = "0x".substr($hash,-40);
        return new Sender($this->addressFactory->makeEthereumAddress($address),$privateKeyHex);
    }
}