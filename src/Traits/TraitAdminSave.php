<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;
use VitesseCms\User\Enum\AclEnum;

trait TraitAdminSave
{
    public function saveAction(string $id): void
    {
        echo 'is save action';
        die();
    }
}