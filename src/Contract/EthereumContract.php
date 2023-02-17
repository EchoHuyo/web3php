<?php

namespace Web3php\Contract;


use Web3\Contract;
use Web3\Utils;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Ethereum\Ethereum;
use Web3php\Contract\Call\EthereumContractCall;
use Web3php\Contract\Config\ContractConfig;
use Web3php\Contract\Event\Item\DecodeInputItem;
use Web3php\Contract\Send\EthereumContractSend;

class EthereumContract extends AbstractContract
{
    /**
     * @var Contract
     */
    protected Contract $contract;

    /**
     * @var array<string,array>
     */
    protected array $eventList = [];

    /**
     * @param Ethereum $chain
     * @param ContractConfig $config
     */
    public function __construct(protected Ethereum $chain, protected ContractConfig $config)
    {
        $this->reloadConfig($config);
        $this->contractCall = new EthereumContractCall($this);
        $this->contractSend = new EthereumContractSend($this);
    }

    /**
     * @return Ethereum
     */
    public function getChain(): Ethereum
    {
        return $this->chain;
    }

    /**
     * @param ContractConfig $config
     * @return void
     */
    public function reloadConfig(ContractConfig $config):void
    {
        $this->contractAddress = $config->address;
        $this->config = $config;
        $this->contract = (new Contract($this->chain->getWeb3()->getProvider(), $this->config->abi))->at($this->contractAddress->getAddress());
        $this->loadEvent();
    }

    /**
     * @param AddressInterface $address
     * @return void
     */
    public function setContractAddress(AddressInterface $address): void
    {
        $this->contractAddress = $address;
        $this->contract = (new Contract($this->chain->getWeb3()->getProvider(), $this->config->abi))->at($address->getAddress());
    }

    /**
     * @return Contract
     */
    public function getContract(): Contract
    {
        return $this->contract;
    }

    /**
     * @return void
     */
    public function loadEvent(): void
    {
        $this->eventList = [];
        if (!empty($this->config->event)) {
            $events = $this->contract->getEvents();
            foreach ($events as $key => $event) {
                if (in_array($key, $this->config->event)) {
                    $signature = mb_strtolower(Utils::stripZero($this->contract->getEthabi()->encodeEventSignature($event)));
                    $this->eventList[$signature] = [
                        'name' => $key,
                        'event' => $event,
                    ];
                }
            }
        }
    }

    /**
     * @param string $encodeInput
     * @return DecodeInputItem
     */
    public function decodeInput(string $encodeInput): DecodeInputItem
    {
        $functions = $this->contract->getFunctions();
        $inputs = [];
        $functionName = "";
        foreach ($functions as $function) {
            $functionName = Utils::jsonMethodToString($function);
            $functionNameCode = $this->contract->getEthabi()->encodeFunctionSignature($functionName);
            if ($functionNameCode == substr($encodeInput, 0, 10)) {
                $functionName = $function['name'];
                $encodeInput = '0x' . substr($encodeInput, 10, mb_strlen($encodeInput));
                $types = [];
                $key = [];
                if (isset($function['inputs'])) {
                    foreach ($function['inputs'] as $input) {
                        if (isset($input['type'])) {
                            $types[] = $input['type'];
                            $key[] = $input['name'];
                        }
                    }
                }
                $inputs = array_combine($key, $this->contract->getEthabi()->decodeParameters($types, $encodeInput));
            }
        }
        return new DecodeInputItem($functionName,$inputs);
    }

    public function getContractEvent(string $signature): array
    {
        $signature = mb_strtolower(Utils::stripZero($signature));
        return $this->eventList[$signature] ?? [];
    }

    /**
     * @return string[]
     */
    public function getTopic0List(): array
    {
        $data = [];
        foreach ($this->eventList as $signature => $item) {
            $data[] = '0x' . $signature;
        }
        return $data;
    }

    public function getConfig(): ContractConfig
    {
        return $this->config;
    }

    /**
     * @param array $topics
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics): DecodeInputItem
    {
        $signature = array_shift($topics);
        $event = $this->getContractEvent($signature);
        $inputs = [];
        $name = "";
        if ($event) {
            $name = $event['name'];
            $contractEvent = $event['event'];
            $key = $valueInput = [];
            foreach ($contractEvent['inputs'] as $input) {
                if ($input['indexed']) {
                    $param = array_shift($topics);
                    $value = current($this->contract->getEthabi()->decodeParameters([$input['type']], $param));
                    $inputs[$input['name']] = $this->format($input['type'], $input['name'], $value);
                } else {
                    $valueInput[] = $input['type'];
                    $key[] = $input['name'];
                }
            }
            if (!empty($valueInput)) {
                $valueData = $this->contract->getEthabi()->decodeParameters($valueInput, $topics['data']);
                $inputsData = [];
                foreach ($valueInput as $k => $type) {
                    $inputsData[$key[$k]] = $this->format($type, $key[$k], $valueData[$k]);
                }
                $inputs = array_merge($inputs, $inputsData);
            }
        }
        return new DecodeInputItem($name, $inputs);
    }

}