<?php

namespace Web3php\Contract\Type\ERC20And721Transfer;

use Web3php\Chain\Utils\Tool\SignatureTool;
use Web3php\Contract\EthereumContract;
use Web3php\Contract\Event\EventFormatParamInterface;
use Web3php\Contract\Event\Item\DecodeInputItem;

class ERC20And721TransferEventContract extends EthereumContract
{
    protected string $signature = "0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef";

    protected array $erc20Event = [
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
    ];

    protected array $erc721Event = [
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
    ];

    /**
     * @param array $topics
     * @param string $data
     * @param EventFormatParamInterface|null $eventFormatParam
     * @return DecodeInputItem
     */
    public function decodeEvent(array $topics, string $data, ?EventFormatParamInterface $eventFormatParam = null): DecodeInputItem
    {
        $signature = array_shift($topics);
        $deInputs = null;
        $name = "";
        if (SignatureTool::signatureCompare($signature, $this->signature)) {
            $name = "Transfer";
            $inputs = $this->erc20Event;
            if (count($topics) > 2) {
                $inputs = $this->erc721Event;
            }
            $deInputs = $this->eventDecode($inputs,$topics,$data,$eventFormatParam);
        }
        return new DecodeInputItem($name, $deInputs);
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}