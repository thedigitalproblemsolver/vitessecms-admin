<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Forms;

use VitesseCms\Core\Utils\UiUtils;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

final class AdminlistForm extends AbstractForm implements AdminlistFormInterface
{
    public function renderForm(
        string $action,
        string $formName = null,
        bool $noAjax = false,
        bool $newWindow = false
    ): string {
        if (0 === $this->count()) {
            return '';
        }

        $this->setColumn(12, 'label', UiUtils::getScreens());
        $this->setColumn(12, 'input', UiUtils::getScreens());
        $this->setAjaxFunction('admin.fillAdminList');
        $this->addSubmitButton('%ADMIN_FILTER%')
            ->addEmptyButton('%FORM_EMPTY%');

        return parent::renderForm($action, $formName, $noAjax, $newWindow);
    }

    public function addNameField(): void
    {
        $this->addText(
            '%CORE_NAME%',
            'filter[name.'.$this->configuration->getLanguageShort().']'
        );
    }

    public function addPublishedField(): void
    {
        $this->addDropdown(
            '%ADMIN_PUBLISHED_STATE%',
            'filter[published]',
            (new Attributes())->setOptions([
                ['value' => '', 'label' => '%ADMIN_NO_SELECTION%'],
                ['value' => 'true', 'label' => '%ADMIN_PUBLISHED%'],
                ['value' => 'false', 'label' => '%ADMIN_UNPUBLISHED%'],
            ])
        );
    }
}
