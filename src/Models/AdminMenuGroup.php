<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

use VitesseCms\Datagroup\Models\DatagroupIterator;

final class AdminMenuGroup
{
    public function __construct(
        private readonly string $label,
        private readonly string $key,
        private readonly DatagroupIterator $datagroups
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDatagroups(): DatagroupIterator
    {
        return $this->datagroups;
    }
}
