<?php
namespace Web3php\Chain\Utils\JsonRpc;

interface JsonRpcInterface
{
    /**
     * @return string
     */
    public function __toString():string;

    /**
     * @return array
     */
    public function toPayload():array;

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id):self;

    /**
     * @return int
     */
    public function getId():int;

    /**
     * @return string
     */
    public function getRpcVersion():string;

    /**
     * @return string
     */
    public function getMethod():string;

    /**
     * @param array $arguments
     * @return self
     */
    public function setArguments(array $arguments):self;

    /**
     * @return array
     */
    public function getArguments():array;

    public function toString(): string;

}