<?php

namespace Web3php\Contract\Event\EventSignature;

use Web3php\Contract\Event\DecodeEventInterface;

interface EventSignatureInterface
{
    /**
     * @param string $signature
     * @return DecodeEventInterface|null
     */
    public function retrieveEventSignature(string $signature): ?DecodeEventInterface;
}