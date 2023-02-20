<?php
require_once __DIR__.'/../vendor/autoload.php';

use Web3php\Address\AddressFactory;
use Web3p\EthereumUtil\Util;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Constants\Enums\Address\AddressType;

$addressFactory = new AddressFactory(new Util(),new TronAddressUtil());
$ethAddress = $addressFactory->make(AddressType::EthereumAddress,"0x7e7d58269163a68a7b6f270103e7c038961715d0");
var_dump($ethAddress->getAddress());
$tronAddress = $addressFactory->make(AddressType::TronAddress,"TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj");
var_dump($tronAddress->getAddress());
$ethAddress2 = $addressFactory->makeEthereumAddress("0x7e7d58269163a68a7b6f270103e7c038961715d0");
var_dump($ethAddress2->getAddress());
$tronAddress2 = $addressFactory->makeTronAddress("TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj");
var_dump($tronAddress2->getAddress());

$flag = $addressFactory->compare($ethAddress,"0x7e7d58269163a68a7b6f270103e7c038961715d0");
var_dump($flag);

$flag = $addressFactory->compare($ethAddress,"TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj");
var_dump($flag);
$ethToTronAddress = $addressFactory->ethereumToTron($ethAddress);
var_dump($ethToTronAddress->getAddress());
$tronToEthAddress = $addressFactory->tronToEthereum($tronAddress);
var_dump($tronToEthAddress->getAddress());

$flag = $addressFactory->compare($tronAddress,$tronToEthAddress->getAddress());
var_dump($flag);
$flag = $addressFactory->compare($ethAddress,$ethToTronAddress->getAddress());
var_dump($flag);
var_dump("-----------------");
$sender = $addressFactory->generateAddress();
var_dump($sender->privateKey);
$ethAddress3 = $addressFactory->privateKeyToAddress(AddressType::EthereumAddress,$sender->privateKey);
$tronAddress3 = $addressFactory->privateKeyToAddress(AddressType::TronAddress,$sender->privateKey);
var_dump($ethAddress3->getAddress());
var_dump($tronAddress3->getAddress());
var_dump($addressFactory->compare($ethAddress3,$tronAddress3->getAddress()));