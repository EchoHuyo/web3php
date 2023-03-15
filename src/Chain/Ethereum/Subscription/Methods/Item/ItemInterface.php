<?php

namespace Web3php\Chain\Ethereum\Subscription\Methods\Item;

interface ItemInterface
{

    /**
     * @return array|null
     */
    public function toArray():?array;
}