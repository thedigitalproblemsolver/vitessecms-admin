<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelPublishableInterface {
    public function togglePublishAction(string $id): void;
    public function getModel(string $id): ?AbstractCollection;
}