<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelEditableInterface {
    public function isEditable(): bool;
    public function editAction(string $id): void;
    public function getEditableModel(string $id): ?AbstractCollection;
}