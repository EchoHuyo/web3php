<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Web3php\Chain\Config\ChainConfig;
use Web3php\Address\AddressFactory;
use Web3p\EthereumUtil\Util;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Chain\ChainFactory;
use Web3php\Chain\Utils\Sender;
use Web3php\Contract\Type\ERC20\ERC20Factory;

$addressFactory = new AddressFactory(new Util(), new TronAddressUtil());
$chainFactory = new ChainFactory($addressFactory);
//$ethConfig = new ChainConfig(""http://rpc.com"", "1234", "1");
//$ethChain = $chainFactory->makeEthereum($ethConfig);
$tronConfig = new ChainConfig("https://api.trongrid.io");
$tronChain = $chainFactory->makeTron($tronConfig);


$erc20Factory = new ERC20Factory();
//$erc20 = $erc20Factory->makeERC20($ethChain,$ethChain->getAddress("0x3d751a1bf89f6083f63ce21d77ce55701d26f93b"));
//var_dump($erc20->decimals());
//var_dump($erc20->fromWei(new \phpseclib\Math\BigInteger(10000000)));
//$erc20->setContractAddress($ethChain->getAddress("0xa701cad50aa5bfdcaf3ec5fb81b40f9ee77e33d2"));
//var_dump($erc20->decimals());
//var_dump($erc20->fromWei(new \phpseclib\Math\BigInteger(100000000000000000)));

//$trc20 = $erc20Factory->makeTRC20($tronChain,$tronChain->getAddress("TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t"));
//var_dump($trc20->decimals());
//var_dump($trc20->fromWei(new \phpseclib\Math\BigInteger(10000000)));
//$trc20->setContractAddress($tronChain->getAddress("TCFLL5dx5ZJdKnWuesXxi1VPwjLVmWZZy9"));
//var_dump($trc20->decimals());
//var_dump($trc20->fromWei(new \phpseclib\Math\BigInteger(1000000000000000)));

