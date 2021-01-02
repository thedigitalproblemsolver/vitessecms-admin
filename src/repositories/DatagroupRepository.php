<?php declare(strict_types=1);

namespace VitesseCms\Admin\Repositories;

use VitesseCms\Core\Models\DatagroupIterator;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;

class DatagroupRepository extends \VitesseCms\Core\Repositories\DatagroupRepository
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
