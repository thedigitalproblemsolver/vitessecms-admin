<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Forms;

use VitesseCms\Form\Interfaces\AbstractFormInterface;

interface AdminlistFormInterface extends AbstractFormInterface
{
    public function addNameField(): void;

    public function addPublishedField(): void;
}
