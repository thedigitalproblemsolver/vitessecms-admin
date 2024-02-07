<?php
declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelEditableInterface extends AdminModelSaveInterface
{
    public function editAction(string $itemId): void;

    public function getModel(string $id): ?AbstractCollection;

    public function getModelForm(): AdminModelFormInterface;
}
