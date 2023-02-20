<?php

use Web3p\EthereumUtil\Util;
use Web3php\Address\AddressFactory;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Chain\ChainFactory;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Contract\Event\ContractEventFactory;
use Web3php\Contract\Event\EventLoadInterface;
use Web3php\Contract\Type\ERC20\ERC20Factory;

require_once __DIR__ . '/../vendor/autoload.php';


$addressFactory = new AddressFactory(new Util(), new TronAddressUtil());
$chainFactory = new ChainFactory($addressFactory);
$ethConfig = new ChainConfig("http://rpc.com", "1", "1");
$ethChain = $chainFactory->makeEthereum($ethConfig);

$contractEventFactory = new ContractEventFactory();
$contractEvent = $contractEventFactory->make($ethChain,"test");

class testEvent implements EventLoadInterface{

    public function __construct(
        protected \Web3php\Contract\AbstractContract $contract
    )
    {

    }

    public function huddle(string $hash, \Web3php\Address\AddressInterface $contractAddress, string $eventName, int $logKey, array $data): void
    {
        var_dump("编码打印");
        var_dump(
            $hash,
            $contractAddress->getAddress(),
            $eventName,
            $logKey,
            $data
        );
    }

    public function decodeEvent(array $topics,string $data): \Web3php\Contract\Event\Item\DecodeInputItem
    {
        return $this->contract->decodeEvent($topics,$data);
    }

    public function getEventSignature(): string
    {
        return "0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef";
    }
}

class testContractEvent implements \Web3php\Contract\Event\ContractEventLoadInterface {

    public function __construct(
        protected \Web3php\Contract\AbstractContract $contract
    )
    {

    }

    public function huddle(string $hash, \Web3php\Address\AddressInterface $contractAddress, string $eventName, int $logKey, array $data): void
    {
        var_dump("合约打印");
        var_dump(
            $hash,
            $contractAddress->getAddress(),
            $eventName,
            $logKey,
            $data
        );
    }

    public function decodeEvent(array $topics,string $data): \Web3php\Contract\Event\Item\DecodeInputItem
    {
        return $this->contract->decodeEvent($topics,$data);
    }

    public function getContractAddress(): \Web3php\Address\AddressInterface
    {
        return $this->contract->getContractAddress();
    }
}
$erc20Factory = new ERC20Factory();

$events = [
    new testEvent($erc20Factory->makeERC20($ethChain,$ethChain->getAddress("0x6029058d62ada392a928569354de7ae35d8e7897")))
];
$ContractEvents = [
    new testContractEvent($erc20Factory->makeERC20($ethChain,$ethChain->getAddress("0x6029058d62ada392a928569354de7ae35d8e7897")))
];
$contractEvent->loadEvent($events);
$contractEvent->loadContractEvents($ContractEvents);
$data = $contractEvent->listener("0xe4711a46046afadb23e039697cf75d7790bb36d52ad09d977314c0a8df660774");
var_dump($data);



