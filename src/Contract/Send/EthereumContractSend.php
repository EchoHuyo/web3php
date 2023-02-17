<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Web3php\Contract\Send;

use Web3\Utils;
use Web3php\Chain\Utils\Receiver;
use Web3php\Constants\Errors\ChainErrors\ErrorCode;
use Web3php\Contract\EthereumContract;
use Web3php\Exception\ChainException;

class EthereumContractSend implements ContractSendInterface
{
    public function __construct(protected EthereumContract $contract)
    {
    }

    public function __call(string $name, array $arguments): string
    {
        $data = $this->encode($name, $arguments);
        $receiver = new Receiver(
            $this->contract->getContractAddress()
        );
        return $this->contract->getChain()->sendTransaction($receiver, $data);
    }

    // 获取 合约 方法 配置
    protected function getFunctionClass(string $functionName): array
    {
        $result = [];
        foreach ($this->contract->getContract()->getFunctions() as $item) {
            if ($item['name'] == $functionName) {
                $result = $item;
                break;
            }
        }
        if (empty($result)) {
            throw new ChainException(ErrorCode::NOT_FOUND_CONTRACT_FUNCTION);
        }
        return $result;
    }

    protected function encode(string $functionName, array $encodeData): string
    {
        $contract = $this->contract->getContract();
        $functionClass = $this->getFunctionClass($functionName);
        $functionSignature = $contract->getEthabi()->encodeFunctionSignature($functionClass);
        return $functionSignature . Utils::stripZero($contract->getEthabi()->encodeParameters($functionClass, $encodeData));
    }
}
