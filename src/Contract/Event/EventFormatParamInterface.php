<?php

namespace Web3php\Contract\Event;

interface EventFormatParamInterface
{
    /**
     * @param string $type
     * @param string $paramName
     * @param mixed $param
     * @return mixed
     */
    public function formatParam(string $type,string $paramName, mixed $param): mixed;
}