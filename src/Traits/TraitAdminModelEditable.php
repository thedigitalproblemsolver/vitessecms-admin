<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelEditable
{
    use TraitAdminModelSave;

    protected bool $isEditable = true;
    protected array $formParams = [];

    public function editAction(string $itemId): void
    {
        $modelForm = $this->getModelForm();
        $modelForm->setEntity($this->getModel($itemId));
        $modelForm->buildForm();
        $this->eventsManager->fire(self::class.':beforeEditModel', $this, $modelForm->getEntity());
        $this->addFormParams(
            'form',
            $modelForm->renderForm(
                $this->urlService->getBaseUri().'admin/'.$this->router->getModuleName(
                ).'/'.$this->router->getControllerName().'/save/'.$itemId
            )
        );
        $this->addFormParams('model', $modelForm->getEntity());

        $this->viewService->set(
            'content',
            $this->eventsManager->fire(
                ViewEnum::RENDER_TEMPLATE_EVENT,
                new RenderTemplateDTO(
                    $this->getTemplate(),
                    $this->getTemplatePath(),
                    $this->formParams
                )
            )
        );
    }

    public function addFormParams(string $key, $value): void
    {
        $this->formParams[$key] = $value;
    }

    protected function getTemplate(): string
    {
        return 'adminModelForm';
    }

    protected function getTemplatePath(): string
    {
        return '';
    }
}