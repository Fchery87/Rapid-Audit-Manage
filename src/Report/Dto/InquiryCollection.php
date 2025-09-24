<?php

namespace App\Report\Dto;

use JsonSerializable;

/**
 * @implements \IteratorAggregate<int, Inquiry>
 */
final class InquiryCollection implements \IteratorAggregate, JsonSerializable
{
    /**
     * @param Inquiry[] $inquiries
     */
    public function __construct(
        private readonly int $total,
        private readonly int $limit,
        private readonly array $inquiries
    ) {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->inquiries);
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
        return count($this->inquiries);
    }

    /**
     * @return Inquiry[]
     */
    public function all(): array
    {
        return $this->inquiries;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'limit' => $this->limit,
            'returned' => $this->count(),
            'accounts' => array_map(static fn(Inquiry $inquiry) => $inquiry->toRow(), $this->inquiries),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
