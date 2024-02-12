<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelCopyableInterface
{
    public function getModel(string $modelId): ?AbstractCollection;
}
