<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Web3php\Address\Utils;

use IEXBase\TronAPI\Support\Base58Check;
use IEXBase\TronAPI\Support\Hash;
use IEXBase\TronAPI\TronAwareTrait;

class TronAddressUtil
{
    use TronAwareTrait;

    public const ADDRESS_SIZE = 34;

    public const ADDRESS_PREFIX_BYTE = 0x41;

    public static function isAddress(string $address): bool
    {
        if (strlen($address) !== self::ADDRESS_SIZE) {
            return false;
        }

        $address = Base58Check::decode($address, 0, 0, false);
        $utf8 = hex2bin($address);

        if (strlen($utf8) !== 25) {
            return false;
        }
        if (strpos($utf8, chr(self::ADDRESS_PREFIX_BYTE)) !== 0) {
            return false;
        }

        $checkSum = substr($utf8, 21);
        $address = substr($utf8, 0, 21);

        $hash0 = Hash::SHA256($address);
        $hash1 = Hash::SHA256($hash0);
        $checkSum1 = substr($hash1, 0, 4);

        if ($checkSum === $checkSum1) {
            return true;
        }
        return false;
    }
}
