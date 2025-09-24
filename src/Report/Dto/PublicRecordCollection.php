<?php

namespace App\Report\Dto;

use JsonSerializable;

/**
 * @implements \IteratorAggregate<int, PublicRecord>
 */
final class PublicRecordCollection implements \IteratorAggregate, JsonSerializable
{
    /**
     * @param PublicRecord[] $records
     */
    public function __construct(
        private readonly int $total,
        private readonly int $limit,
        private readonly array $records
    ) {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->records);
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
        return count($this->records);
    }

    /**
     * @return PublicRecord[]
     */
    public function all(): array
    {
        return $this->records;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'limit' => $this->limit,
            'returned' => $this->count(),
            'records' => array_map(static fn(PublicRecord $record) => $record->toArray(), $this->records),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
