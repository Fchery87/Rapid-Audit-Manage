<?php

namespace App\Report\Dto;

use JsonSerializable;

/**
 * @implements \IteratorAggregate<int, DerogatoryAccount>
 */
final class DerogatoryAccountCollection implements \IteratorAggregate, JsonSerializable
{
    /**
     * @param DerogatoryAccount[] $accounts
     */
    public function __construct(
        private readonly int $total,
        private readonly int $limit,
        private readonly array $accounts
    ) {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->accounts);
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function count(): int
    {
        return count($this->accounts);
    }

    /**
     * @return DerogatoryAccount[]
     */
    public function all(): array
    {
        return $this->accounts;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'limit' => $this->limit,
            'returned' => $this->count(),
            'accounts' => array_map(static fn(DerogatoryAccount $account) => $account->toArray(), $this->accounts),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
