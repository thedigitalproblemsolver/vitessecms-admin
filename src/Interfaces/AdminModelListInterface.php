<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindValueIterator;

interface AdminModelListInterface
{
    /**
     * @param FindValueIterator|null $findValueIterator
     * @return \ArrayIterator<int, AbstractCollection>
     */
    public function getModelList(?FindValueIterator $findValueIterator): \ArrayIterator;

    public function adminListAction(): void;
}
