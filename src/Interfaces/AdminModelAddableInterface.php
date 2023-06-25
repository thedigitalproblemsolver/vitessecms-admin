<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

use VitesseCms\Database\AbstractCollection;

interface AdminModelAddableInterface extends AdminModelSaveInterface {
    public function addAction(): void;
}