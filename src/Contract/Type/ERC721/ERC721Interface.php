<?php

declare(strict_types=1);

namespace Web3php\Contract\Type\ERC721;


use Web3php\Address\AddressInterface;

interface ERC721Interface
{
    public function name(): string;

    public function symbol(): string;

    public function balanceOf(AddressInterface $address): string;

    public function ownerOf(int $tokenId): string;

    public function approve(AddressInterface $to, int $tokenId): string;

    public function safeTransferFrom(AddressInterface $from, AddressInterface $to, int $tokenId): string;

    public function transferFrom(AddressInterface $from, AddressInterface $to, int $tokenId): string;

    public function getApproved(int $tokenId): string;

    public function setApprovalForAll(AddressInterface $operator, bool $approved);

    public function isApprovedForAll(AddressInterface $account, AddressInterface $operator): bool;

    public function tokenURI(int $tokenId): string;
}
