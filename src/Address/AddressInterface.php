<?php

namespace Web3php\Address;

interface AddressInterface
{
    /**
     * @param string $address
     * @return bool
     */
    public function compare(string $address): bool;

    /**
     * @return string
     */
    public function getAddress(): string;

    /**
     * @param string $address
     * @return bool
     */
    public static function isAddress(string $address): bool;

}