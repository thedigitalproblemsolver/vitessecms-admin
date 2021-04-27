<?php declare(strict_types=1);

namespace VitesseCms\Admin\Forms;

use VitesseCms\Form\Interfaces\AbstractFormInterface;

interface AdminlistFormInterface
{
    public function addNameField(AbstractFormInterface $form): void;

    public function addPublishedField(AbstractFormInterface $form): void;
}
