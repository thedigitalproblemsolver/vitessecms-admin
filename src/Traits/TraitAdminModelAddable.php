<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelAddable
{
    protected bool $isAddable = true;

    public function addAction(): void
    {
        $form = $this->getModelForm();
        $form->buildForm();
        $renderedForm = $form->renderForm(
            'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName() . '/save/new'
        );
        $this->viewService->set('content', $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
            'adminModelForm',
            '',
            ['form' => $renderedForm]
        )));
    }
}