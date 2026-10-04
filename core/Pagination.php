<?php

namespace core;

class Pagination
{
    private readonly int $page;
    private readonly int $limit;
    private array $meta;

    public function __construct(?int $page = null, ?int $limit = null)
    {
        $this->page = ($page === null || $page < 1) ? 1 : $page;
        $this->limit = ($limit === null || $limit < 1) ? 10 : $limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(int $count, bool $availableNextPage): void
    {
        $this->meta = [
            'page' => $this->page,
            'limit' => $this->limit,
            'count' => $count,
            'next' => $availableNextPage ? $this->page + 1 : null,
            'prev' => $this->page == 1 ? null : $this->page - 1,
        ];
    }
}