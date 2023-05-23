<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\Models\FindValueIterator;

interface AdminModelListInterface {
    public function getModelList( ?FindValueIterator $findValueIterator, int $limit = 25): \ArrayIterator;
    public function adminListAction(): void;
}