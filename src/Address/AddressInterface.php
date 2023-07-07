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
     * @deprecated
     */
    public function getAddress(): string;

    /**
     * @return string
     */
    public function toString(): string;
    /**
     * @param string $address
     * @return bool
     */
    public static function isAddress(string $address): bool;

}