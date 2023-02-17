<?php

declare(strict_types=1);

namespace Web3php\Contract\Send;

interface ContractSendInterface
{
    public function __call(string $name, array $arguments);
}
