<?php

declare(strict_types=1);

namespace Web3php\Contract\Type\ERC1155;

use Web3php\Address\AddressInterface;

interface ERC1155Interface
{
    public function balanceOf(AddressInterface $address, int $tokenId): int;

    /**
     * @param AddressInterface[] $accounts
     * @param int[] $ids
     */
    public function balanceOfBatch(array $accounts, array $ids): array;

    public function setApprovalForAll(AddressInterface $operator, bool $approved): string;

    public function isApprovedForAll(AddressInterface $account, AddressInterface $operator): bool;

    public function safeTransferFrom(AddressInterface $from, AddressInterface $to, int $id, int $amount, string $data): string;

    /**
     * @param AddressInterface $from
     * @param AddressInterface $to
     * @param int[] $ids
     * @param int[] $amounts
     * @param string $data
     * @return string
     */
    public function safeBatchTransferFrom(AddressInterface $from, AddressInterface $to, array $ids, array $amounts, string $data): string;

    public function uri(int $tokenId): string;
}
