<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelEditable
{
    use TraitAdminModelSave;

    protected bool $isEditable = true;
    protected array $formParams = [];

    public function editAction(string $id): void
    {
        $modelForm = $this->getModelForm();
        $modelForm->setEntity($this->getModel($id));
        $modelForm->buildForm();
        $this->addFormParams('form', $modelForm->renderForm(
            $this->urlService->getBaseUri().'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName() . '/save/'.$id
        ));
        $this->viewService->set('content', $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
            $this->getTemplate(),
            $this->getTemplatePath(),
            $this->formParams
        )));
    }

    protected function getTemplate(): string
    {
        return 'adminModelForm';
    }

    protected function getTemplatePath(): string
    {
        return '';
    }

    protected function addFormParams(string $key, $value) {
        $this->formParams[$key] = $value;
    }
}