<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelSaveInterface
{
    public function getModel(string $modelId): ?AbstractCollection;

    public function saveAction(string $modelid): void;

    public function getModelForm(): AdminModelFormInterface;
}