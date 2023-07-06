<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelEditable
{
    protected bool $isEditable = true;

    public function editAction(string $id): void
    {
        $modelForm = $this->getModelForm();
        $modelForm->setEntity($this->getModel($id));
        $modelForm->buildForm();
        $renderedForm = $modelForm->renderForm(
            $this->urlService->getBaseUri().'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName() . '/save/'.$id
        );
        $this->viewService->set('content', $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
            'adminModelForm',
            '',
            ['form' => $renderedForm]
        )));
    }
}