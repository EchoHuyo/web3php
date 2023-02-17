<?php

namespace Web3php\Contract\Event;

interface EventLoadInterface extends BaseEventInterface
{
    /**
     * @return string
     */
    public function getEventSignature(): string;
}