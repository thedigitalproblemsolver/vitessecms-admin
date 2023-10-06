<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelSaveInterface {
    public function getModel(string $id): ?AbstractCollection;
    public function saveAction(string $id): void;
    public function getModelForm():AdminModelFormInterface;
}