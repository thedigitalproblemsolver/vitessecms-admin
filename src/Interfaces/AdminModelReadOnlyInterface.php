<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelReadOnlyInterface
{
    public function readOnlyAction(string $modelId): void;

    public function getModel(string $modelId): ?AbstractCollection;
}
