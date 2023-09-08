<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\Interfaces\AbstractFormInterface;

interface AdminModelEditableInterface extends AdminModelSaveInterface {
    public function editAction(string $id): void;
    public function getModel(string $id): ?AbstractCollection;
    public function getModelForm(): AdminModelFormInterface;
}
