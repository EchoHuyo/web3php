<?php

namespace Web3php\Chain\Utils\JsonRpc;

use Web3php\Exception\JsonRpcInvalidArgumentException;

class AbstractJsonRpc implements JsonRpcInterface
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $version = '2.0';

    /**
     * @var string
     */
    protected string $method;

    /**
     * @var array
     */
    protected array $arguments = [];

    public function toPayload(): array
    {
        if (empty($this->method)) {
            throw new JsonRpcInvalidArgumentException('Please check the method set properly.');
        }
        if (empty($this->id)) {
            $this->id = rand();
        }
        $rpc = [
            'id' => $this->id,
            'jsonrpc' => $this->version,
            'method' => $this->method
        ];
        if (count($this->arguments) > 0) {
            $rpc['params'] = $this->arguments;
        }
        return $rpc;
    }

    public function setArguments(array $arguments): JsonRpcInterface
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $payload = $this->toPayload();
        return json_encode($payload);
    }

    public function setId(int $id): JsonRpcInterface
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRpcVersion(): string
    {
        return $this->version;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}