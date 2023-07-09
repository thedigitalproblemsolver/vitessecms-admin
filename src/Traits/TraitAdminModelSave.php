<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;

trait TraitAdminModelSave
{
    public function saveAction(string $id): void
    {
        $modelForm = $this->getModelForm();
        $modelForm->setEntity($this->getModel($id));
        $modelForm->buildForm();
        $modelForm->bind($this->request->getPost());
        if($modelForm->validate()) {
            $this->saveModel($modelForm);
        } else {
            $this->flashService->setError('ADMIN_ITEM_NOT_SAVED');
        }

        $this->redirect($this->urlService->getBaseUri().'admin/' . $this->routerService->getModuleName() . '/' . $this->routerService->getControllerName() . '/edit/'.$model->getId());
    }

    private function saveModel(AdminModelFormInterface $modelForm):void
    {
        $model = $modelForm->getEntity();
        $model->save();
        $this->logService->write($model->getId(), get_class($model), 'Item saved');
        $this->flashService->setSucces('ADMIN_ITEM_SAVED');
    }
}