<?php

namespace Web3php\Contract;


use Web3\Contract;
use Web3\Utils;
use Web3php\Address\AddressInterface;
use Web3php\Chain\Ethereum\Ethereum;
use Web3php\Chain\Utils\Tool\SignatureTool;
use Web3php\Contract\Call\EthereumContractCall;
use Web3php\Contract\Config\ContractConfig;
use Web3php\Contract\Event\EventFormatParamInterface;
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
    public function reloadConfig(ContractConfig $config): void
    {
        $this->contractAddress = $config->address;
        $this->config = $config;
        $this->contract = (new Contract($this->chain->getWeb3()->getProvider(), $this->config->abi))->at($this->contractAddress->getAddress());
        $this->contractCall = new EthereumContractCall($this);
        $this->contractSend = new EthereumContractSend($this);
        $this->loadEvent();
    }

    /**
     * @param AddressInterface $address
     * @return void
     */
    public function setContractAddress(AddressInterface $address): void
    {
        $this->config->address = $address;
        $this->reloadConfig($this->config);
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
                    $signature = SignatureTool::formatSignature($this->contract->getEthabi()->encodeEventSignature($event));
                    $this->eventList[$signature] = [
                        'name' => $key,
                        'event' => $event,
                    ];
                }
            }
        }
    }



    public function getContractEvent(string $signature): array
    {
        $signature = SignatureTool::formatSignature($signature);
        return $this->eventList[$signature] ?? [];
    }

    /**
     * @param bool $isPrefix
     * @return array
     */
    public function getEventSignatureList(bool $isPrefix = false): array
    {
        $result = [];
        foreach ($this->eventList as $signature => $item) {
            $result[] = $isPrefix ? '0x' . $signature : $signature;
        }
        return $result;
    }

    public function getConfig(): ContractConfig
    {
        return $this->config;
    }

    /**
     * @param array $topics
     * @param string $data
     * @param EventFormatParamInterface|null $eventFormatParam
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics, string $data, ?EventFormatParamInterface $eventFormatParam = null): DecodeInputItem
    {
        $signature = array_shift($topics);
        $event = $this->getContractEvent($signature);
        $inputs = null;
        $name = "";
        if ($event) {
            $name = $event['name'];
            $contractEvent = $event['event'];
            $inputs = $this->eventDecode($contractEvent['inputs'], $topics, $data, $eventFormatParam);
        }
        return new DecodeInputItem($name, $inputs);
    }

    /**
     * @param array $inputs
     * @param array $topics
     * @param string $data
     * @param EventFormatParamInterface|null $eventFormatParam
     * @return array
     */
    protected function eventDecode(array $inputs, array $topics, string $data, ?EventFormatParamInterface $eventFormatParam = null): array
    {
        $valueInput = [];
        $deInputs = [];
        $ethAbi = $this->contract->getEthabi();
        foreach ($inputs as $input) {
            if ($input['indexed']) {
                $param = array_shift($topics);
                $value = current($ethAbi->decodeParameters([$input['type']], $param));
                $deInputs[$input['name']] = $eventFormatParam ?
                    $eventFormatParam->formatParam($input['type'], $input['name'], $value):$value;
            } else {
                $valueInput[] = [$input['type'], $input['name']];
            }
        }
        if (!empty($valueInput)) {
            $inputsData = $this->decodeData($valueInput,$data,$eventFormatParam);
            $deInputs = array_merge($deInputs, $inputsData);
        }
        return $deInputs;
    }

    /**
     * @param string $encodeInput
     * @param EventFormatParamInterface|null $eventFormatParam
     * @return DecodeInputItem
     */
    public function decodeInput(string $encodeInput,?EventFormatParamInterface $eventFormatParam = null): DecodeInputItem
    {
        $functions = $this->contract->getFunctions();
        $inputs = [];
        $functionName = "";
        $ethAbi = $this->contract->getEthabi();
        foreach ($functions as $function) {
            $functionName = Utils::jsonMethodToString($function);
            $functionNameCode = $ethAbi->encodeFunctionSignature($functionName);
            if (SignatureTool::signatureCompare($functionNameCode, substr($encodeInput, 0, 10))) {
                $functionName = $function['name'];
                $encodeInput = '0x' . substr($encodeInput, 10, mb_strlen($encodeInput));
                $valueInput = [];
                if (isset($function['inputs'])) {
                    foreach ($function['inputs'] as $input) {
                        if (isset($input['type'])) {
                            $valueInput[] = [$input['type'], $input['name']];
                        }
                    }
                }
                $inputs = $this->decodeData($valueInput,$encodeInput,$eventFormatParam);
            }
        }
        return new DecodeInputItem($functionName, $inputs);
    }

    /**
     * @param array  $valueInput <[type,name]>
     * @param string $data
     * @param EventFormatParamInterface|null $eventFormatParam
     * @return array
     */
    protected function decodeData(array $valueInput,string $data,?EventFormatParamInterface $eventFormatParam = null):array
    {
        $valueData = $this->contract->getEthabi()->decodeParameters(array_column($valueInput, 0), $data);
        $inputsData = array_combine(array_column($valueInput, 1), $valueData);
        if ($eventFormatParam) {
            $inputsData = array_map(
                fn ($type, $name, $value) => [$name, $eventFormatParam->formatParam($type, $name, $value)],
                array_column($valueInput, 0),
                array_column($valueInput, 1),
                $valueData
            );
            $inputsData = array_combine(array_column($inputsData, 0), array_column($inputsData, 1));
        }
        return $inputsData;
    }


}