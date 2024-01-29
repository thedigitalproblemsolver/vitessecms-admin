<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Forms;

interface AdminlistFormInterface
{
    public function addNameField(): void;

    public function addPublishedField(): void;
}
