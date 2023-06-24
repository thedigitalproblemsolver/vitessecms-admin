<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\User\Enum\AclEnum;

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
        $this->viewService  ->set('content', $renderedForm);

        /*$model = $this->getModel($id);
        if($model !== null) {
            $model->delete();
            $this->logService->write(
                $model->getId(),
                get_class($model),
                $this->languageService->get('ADMIN_ITEM_DELETED', [$model->getNameField()])
            );
            $this->flashService->setSucces('ADMIN_ITEM_DELETED', [$model->getNameField()]);
        } else {
            $this->flashService->setError('ADMIN_ITEM_NOT_FOUND');
        }

        $this->redirect($this->request->getHTTPReferer());*/
    }
}