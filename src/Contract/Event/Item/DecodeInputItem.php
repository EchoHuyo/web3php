<?php

namespace Web3php\Contract\Event\Item;

class DecodeInputItem
{
    public function __construct(public string $name, public array $decodeDate)
    {

    }
}