<?php

declare(strict_types=1);

namespace Web3php\Contract\Type\ERC1155;

use phpseclib\Math\BigInteger;
use Web3php\Address\AddressInterface;
use Web3php\Contract\EthereumContract;

class ERC1155 extends EthereumContract implements ERC1155Interface
{
    public function symbol(): string
    {
        return current($this->call()->symbol());
    }

    public function balanceOf(AddressInterface $address, int $tokenId): int
    {
        $data = $this->call()->balanceOf($address->getAddress(), new BigInteger($tokenId));
        if ($data) {
            $data = current($data);
        }
        return (int) $data->toString();
    }

    public function balanceOfBatch(array $accounts, array $ids): array
    {
        $addressList = [];
        foreach ($accounts as $account) {
            $addressList[] = $account->getAddress();
        }
        $tokenId = [];
        foreach ($ids as $id) {
            $tokenId[] = new BigInteger($id);
        }
        return $this->call()->balanceOfBatch($addressList, $tokenId);
    }

    public function setApprovalForAll(AddressInterface $operator, bool $approved): string
    {
        return $this->send()->setApprovalForAll($operator->getAddress(), $approved);
    }

    public function isApprovedForAll(AddressInterface $account, AddressInterface $operator): bool
    {
        $data = $this->call()->isApprovedForAll($account->getAddress(), $operator->getAddress());
        if ($data) {
            $data = current($data);
        }
        return (bool) $data;
    }

    public function safeTransferFrom(AddressInterface $from, AddressInterface $to, int $id, int $amount, string $data): string
    {
        return $this->send()->safeTransferFrom($from->getAddress(), $to->getAddress(), new BigInteger($id), new BigInteger($amount), $data);
    }

    public function safeBatchTransferFrom(AddressInterface $from, AddressInterface $to, array $ids, array $amounts, string $data): string
    {
        $tokenId = [];
        foreach ($ids as $id) {
            $tokenId[] = new BigInteger($id);
        }
        $amountList = [];
        foreach ($amounts as $amount) {
            $amountList[] = new BigInteger($amount);
        }
        return $this->send()->safeBatchTransferFrom($from->getAddress(), $to->getAddress(), $tokenId, $amountList, $data);
    }

    public function uri(int $tokenId): string
    {
        $data = $this->call()->uri(new BigInteger($tokenId));
        if ($data) {
            $data = current($data);
        }
        return $data;
    }

    public function getTokenName(int $tokenId): string
    {
        $data = $this->call()->getTokenName(new BigInteger($tokenId));
        if ($data) {
            $data = current($data);
        }
        return $data;
    }
}
