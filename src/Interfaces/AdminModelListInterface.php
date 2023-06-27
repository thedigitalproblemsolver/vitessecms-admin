<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\Models\FindValueIterator;

interface AdminModelListInterface {
    public function getModelList( ?FindValueIterator $findValueIterator): \ArrayIterator;
    public function adminListAction(): void;
}