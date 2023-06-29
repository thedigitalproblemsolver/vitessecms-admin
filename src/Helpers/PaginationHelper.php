<?php declare(strict_types=1);

namespace VitesseCms\Admin\Helpers;

use VitesseCms\Core\Services\UrlService;

class PaginationHelper
{
    public function __construct(
        private readonly \SeekableIterator $seekableIterator,
        private readonly UrlService $urlService,
        private readonly int $offset,
        private readonly int $limit = 10
    )
    {
    }

    public function getSliced(): \LimitIterator
    {
        return new \LimitIterator($this->seekableIterator,$this->offset,$this->limit);
    }

    public function nextOffset(): int
    {
        return $this->offset*2;
    }

    public function count():int
    {
        return $this->seekableIterator->count();
    }

    public function hasNextPage():bool
    {
        return ($this->offset+$this->limit) < $this->count();
    }

    public function hasPreviousPage():bool
    {
        return ($this->offset-$this->limit) >= 0;
    }

    public function getFirstNumberInList() :int
    {
        return $this->offset+1;
    }

    public function getLastNumberInList() :int
    {
        return ($this->offset+$this->limit < $this->count())?$this->offset+$this->limit:$this->count();
    }

    public function getFirstPageUrl(): string
    {
        return $this->urlService->getCurrentUrl();
    }

    public function getLastPageUrl(): string
    {
        $offset = (ceil($this->seekableIterator->count()/$this->limit)*$this->limit)-$this->limit;

        return $this->urlService->addParamsToQuery('offset' ,(string)$offset,$this->urlService->getCurrentUrl());
    }

    public function getNextPageUrl(): string
    {
        return $this->urlService->addParamsToQuery('offset' ,(string)($this->offset+$this->limit),$this->urlService->getCurrentUrl());
    }

    public function getPreviousPageUrl(): string
    {
        return $this->urlService->addParamsToQuery('offset' ,(string)($this->offset-$this->limit),$this->urlService->getCurrentUrl());
    }
}