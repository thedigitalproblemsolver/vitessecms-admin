<?php declare(strict_types=1);

namespace VitesseCms\Admin\Forms;

use VitesseCms\Core\Utils\UiUtils;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;

class AdminlistForm extends AbstractForm implements AdminlistFormInterface
{
    public function renderForm(
        string $action,
        string $formName = null,
        bool $noAjax = false,
        bool $newWindow = false
    ): string
    {
        $this->setColumn(12, 'label', UiUtils::getScreens());
        $this->setColumn(12, 'input', UiUtils::getScreens());
        $this->setAjaxFunction('admin.fillAdminList');
        $this->addSubmitButton('%ADMIN_FILTER%')
            ->addEmptyButton('%FORM_EMPTY%')
        ;

        return parent::renderForm($action, $formName, $noAjax, $newWindow);
    }
    public function addNameField(AbstractFormInterface $form): void
    {
        $form->addText(
            '%CORE_NAME%',
            'filter[name.'.$form->configuration->getLanguageShort().']'
        );
    }

    public function addPublishedField(AbstractFormInterface $form): void
    {
        $form->addDropdown(
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

