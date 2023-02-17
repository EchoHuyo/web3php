<?php

declare(strict_types=1);

namespace Web3php\Contract\Call;

interface ContractCallInterface
{
    public function __call(string $name, array $arguments);
}
