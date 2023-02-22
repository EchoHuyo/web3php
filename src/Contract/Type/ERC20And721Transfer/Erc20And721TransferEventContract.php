<?php

namespace Web3php\Contract\Type\Erc20And721Transfer;

use Web3\Utils;
use Web3php\Contract\EthereumContract;
use Web3php\Contract\Event\AbstractEventDecode;
use Web3php\Contract\Event\Item\DecodeInputItem;

class Erc20And721TransferEventContract extends EthereumContract
{
    protected string $signature = "0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef";

    protected array $erc20Event = [
        "name" => "Transfer",
        "event" => [
            "inputs" => [
                [
                    "indexed" => true,
                    "internalType" => "address",
                    "type" => "address",
                    "name" => "from",
                ],
                [
                    "indexed" => true,
                    "internalType" => "address",
                    "type" => "address",
                    "name" => "to",
                ],
                [
                    "indexed" => false,
                    "internalType" => "uint256",
                    "type" => "uint256",
                    "name" => "value",
                ],
            ]
        ]
    ];

    protected array $erc721Event = [
        "name" => "Transfer",
        "event" => [
            "inputs" => [
                [
                    "indexed" => true,
                    "internalType" => "address",
                    "type" => "address",
                    "name" => "from",
                ],
                [
                    "indexed" => true,
                    "internalType" => "address",
                    "type" => "address",
                    "name" => "to",
                ],
                [
                    "indexed" => true,
                    "internalType" => "uint256",
                    "type" => "uint256",
                    "name" => "tokenId",
                ],
            ]
        ]
    ];

    /**
     * @param array $topics
     * @param string $data
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics, string $data): DecodeInputItem
    {
        $signature = array_shift($topics);
        $inputs = [];
        $name = "";
        if (AbstractEventDecode::signatureCompare($signature, $this->signature)) {
            $event = $this->erc20Event;
            if (count($topics) > 2) {
                $event = $this->erc721Event;
            }
            $name = $event['name'];
            $contractEvent = $event['event'];
            $key = $valueInput = [];
            foreach ($contractEvent['inputs'] as $input) {
                if ($input['indexed']) {
                    $param = array_shift($topics);
                    $value = current($this->contract->getEthabi()->decodeParameters([$input['type']], $param));
                    $inputs["indexed"][$input['name']] = $this->format($input['type'], $input['name'], $value);
                } else {
                    $valueInput[] = $input['type'];
                    $key[] = $input['name'];
                }
            }
            if (!empty($valueInput)) {
                $valueData = $this->contract->getEthabi()->decodeParameters($valueInput, $data);
                $inputsData = [];
                foreach ($valueInput as $k => $type) {
                    $inputsData["data"][$key[$k]] = $this->format($type, $key[$k], $valueData[$k]);
                }
                $inputs = array_merge($inputs, $inputsData);
            }
        }
        return new DecodeInputItem($name, $inputs);
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}