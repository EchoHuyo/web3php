<?php

namespace Web3php\Contract\Type\ERC721;

use phpseclib\Math\BigInteger;
use Web3php\Contract\EthereumContract;
use Web3php\Address\AddressInterface;

class ERC721 extends EthereumContract implements ERC721Interface
{
    public function name(): string
    {
        $data = $this->call()->name();
        if ($data) {
            $data = current($data);
        }
        return $data;
    }

    public function symbol(): string
    {
        $data = $this->call()->symbol();
        if ($data) {
            $data = current($data);
        }
        return $data;
    }

    public function balanceOf(AddressInterface $address): string
    {
        $data = $this->call()->balanceOf($address->toString());
        if ($data) {
            $data = current($data);
        }
        return $data->toString();
    }

    public function ownerOf(int $tokenId): string
    {
        $data = $this->call()->ownerOf(new BigInteger($tokenId));
        if ($data) {
            $data = current($data);
        }
        return $data;
    }

    public function approve(AddressInterface $to, int $tokenId): string
    {
        return $this->send()->approve($to->toString(), new BigInteger($tokenId));
    }

    public function safeTransferFrom(AddressInterface $from, AddressInterface $to, int $tokenId): string
    {
        return $this->send()->safeTransferFrom($from->toString(), $to->toString(), new BigInteger($tokenId));
    }

    public function transferFrom(AddressInterface $from, AddressInterface $to, int $tokenId): string
    {
        return $this->send()->transferFrom($from->toString(), $to->toString(), new BigInteger($tokenId));
    }

    public function getApproved(int $tokenId): string
    {
        $data = $this->call()->getApproved(new BigInteger($tokenId));
        if ($data) {
            $data = current($data);
        }
        return $data;
    }

    public function setApprovalForAll(AddressInterface $operator, bool $approved): string
    {
        return $this->send()->setApprovalForAll($operator->toString(), $approved);
    }

    public function isApprovedForAll(AddressInterface $account, AddressInterface $operator): bool
    {
        $data = $this->call()->isApprovedForAll($account->toString(), $operator->toString());
        if ($data) {
            $data = current($data);
        }
        return (bool)$data;
    }

    public function tokenURI(int $tokenId): string
    {
        $data = $this->call()->tokenURI(new BigInteger($tokenId));
        if ($data) {
            $data = current($data);
        }
        return $data;
    }
}
