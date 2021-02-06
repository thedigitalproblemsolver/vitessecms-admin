<?php declare(strict_types=1);

namespace VitesseCms\Admin\Repositories;

use VitesseCms\Datagroup\Models\DatagroupIterator;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;

class DatagroupRepository extends \VitesseCms\Datagroup\Repositories\DatagroupRepository
{
    public function getBySystemComponent(string $component): ?DatagroupIterator
    {
        return $this->findAll(
            new FindValueIterator([
                new FindValue('parentId', null),
                new FindValue('component', $component)
            ])
        );
    }
}
