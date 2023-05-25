<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelDeletableInterface {
    public function deleteAction(string $id): void;
    public function getModel(string $id): ?AbstractCollection;
}