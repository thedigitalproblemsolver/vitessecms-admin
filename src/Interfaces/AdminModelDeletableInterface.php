<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelDeletableInterface {
    public function isDeletable(): bool;
    public function deleteAction(string $id): void;
    public function getDeletableModel(string $id): ?AbstractCollection;
}