<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Web3php\Chain\Config\ChainConfig;
use Web3php\Address\AddressFactory;
use Web3p\EthereumUtil\Util;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Chain\ChainFactory;
use Web3php\Chain\Utils\Sender;


$addressFactory = new AddressFactory(new Util(), new TronAddressUtil());
$chainFactory = new ChainFactory($addressFactory);
$ethConfig = new ChainConfig("http://rpc.com", "1", "1");
$ethChain = $chainFactory->makeEthereum($ethConfig);
$address = $ethChain->getAddress("0x7e7d58269163a68a7b6f270103e7c038961715d0");
var_dump($address->getAddress());
$tronConfig = new ChainConfig("https://api.trongrid.io");
$tronChain = $chainFactory->makeTron($tronConfig);


//$address = $tronChain->getAddress("417e7d58269163a68a7b6f270103e7c038961715d0");
//$address2 = $tronChain->getAddress("0x7e7d58269163a68a7b6f270103e7c038961715d0");
//$address3 = $tronChain->getAddress("TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj");
//var_dump($address->getAddress());
//var_dump($address2->getAddress());
//var_dump($address3->getAddress());
//$tronChain->setSender(new Sender(
//    $address,"privateKey"
//));
//$sender = $ethChain->getSender();
//var_dump($sender);



//$block = $ethChain->getBlock();
//var_dump($block);
//var_dump($ethChain->getBlockByNumber($block));
//$block = $tronChain->getBlock();
//var_dump($block);
//var_dump($tronChain->getBlockByNumber($block));

//$transaction = $ethChain->getTransaction("0xc8dacf197645e2f8b98364e27e9555974f04cc818535161accde3a0f92a02fcc");
//var_dump($transaction);
//
//$transaction = $tronChain->getTransaction("056e1fce37c01c94ae7e5e8d218bb90bf964c177a89d841b5a9c64022f19833b");
//var_dump($transaction);



//var_dump($ethChain->checkHashStatus("0xc8dacf197645e2f8b98364e27e9555974f04cc818535161accde3a0f92a02fcc"));
//var_dump($tronChain->checkHashStatus("0da613fb486b63809e05ba228864df6378ba3d5047340b6b5a62ac2fab73cbb4"));
//var_dump($tronChain->checkHashStatus("7634461c14b18f94835c9206c54be69f98839bfef8097076b7427ac8918e14f4"));
