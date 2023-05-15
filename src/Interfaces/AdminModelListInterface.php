<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

interface AdminModelListInterface {
    public function getModelList(): \ArrayIterator;
    public function adminListAction(): void;
}