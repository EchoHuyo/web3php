<?php

use Web3p\EthereumUtil\Util;
use Web3php\Address\AddressFactory;
use Web3php\Address\Utils\TronAddressUtil;
use Web3php\Chain\ChainFactory;
use Web3php\Chain\Config\ChainConfig;
use Web3php\Contract\Event\ContractEventFactory;
use Web3php\Contract\Type\Erc20And721Transfer\ERC20And721TransferEventContract;
use Web3php\Contract\Type\Erc20And721Transfer\ERC20And721TransferEventContractFactory;

require_once __DIR__ . '/../vendor/autoload.php';


$addressFactory = new AddressFactory(new Util(), new TronAddressUtil());
$chainFactory = new ChainFactory($addressFactory);
$ethConfig = new ChainConfig("http://rpc.cn", "1", "1");
$ethChain = $chainFactory->makeEthereum($ethConfig);

$contractEventFactory = new ContractEventFactory();


class RetrieveSignature implements \Web3php\Contract\Event\EventSignature\EventSignatureInterface
{

    protected array $signature;

    public function load(test $test)
    {
        $this->signature[\Web3php\Contract\Event\AbstractEventDecode::formatSignature($test->getSignature())] = $test;
    }

    public function retrieveEventSignature(string $signature): ?\Web3php\Contract\Event\DecodeEventInterface
    {
        $signature = \Web3php\Contract\Event\AbstractEventDecode::formatSignature($signature);
        return $this->signature[$signature] ?? null;
    }
}

class RetrieveAddress implements \Web3php\Contract\Event\EventContract\EventContractInterface
{

    protected array $addresses;

    public function load(test $test)
    {
        $this->addresses[$test->getAddress()->getAddress()] = $test;
    }

    public function retrieveContractAddress(\Web3php\Address\AddressInterface $address): ?\Web3php\Contract\Event\DecodeEventInterface
    {
        return $this->addresses[$address->getAddress()] ?? null;
    }
}


class test extends \Web3php\Contract\Event\AbstractEventDecode
{

    protected ERC20And721TransferEventContract $eventContract;

    public function __construct(
        protected \Web3php\Chain\Ethereum\Ethereum        $chain,
        protected ERC20And721TransferEventContractFactory $contractFactory
    )
    {
        $this->eventContract = $this->contractFactory->make($this->chain);
    }

    public function decodeEvent(array $topics, string $data): \Web3php\Contract\Event\Item\DecodeInputItem
    {
        return $this->eventContract->decodeEvent($topics, $data,$this);
    }

    public function huddle(\Web3php\Contract\Event\Item\LogItem $logsItem): void
    {
        var_dump($logsItem);
    }

    public function getSignature(): string
    {
        return $this->eventContract->getSignature();
    }

    public function getAddress(): \Web3php\Address\AddressInterface
    {
        return $this->chain->getAddress("0x6029058D62AdA392A928569354De7ae35D8e7897");
    }
}

$ERC20And721TransferEventContractFactory = new ERC20And721TransferEventContractFactory();
//$erc20And721 = $ERC20And721TransferEventContractFactory->make($ethChain);

$test = new test($ethChain, $ERC20And721TransferEventContractFactory);
$retrieveSignature = new RetrieveSignature();
$retrieveSignature->load($test);

$retrieveAddress = new RetrieveAddress();
$retrieveAddress->load($test);

$contractEvent = $contractEventFactory->make($ethChain, $retrieveSignature, $retrieveAddress);


//$data = $contractEvent->decodeTopic();
//var_dump($data);







