<?php declare(strict_types=1);

namespace VitesseCms\Admin\Interfaces;

interface AdminModelAddableInterface extends AdminModelSaveInterface {
    public function addAction(): void;
}