<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;

trait TraitAdminModelSave
{
    public function saveAction(string $itemId): void
    {
        $modelForm = $this->getModelForm();
        $modelForm->setEntity($this->getModel($itemId));
        $modelForm->buildForm();
        $modelForm->bind($this->request->getPost());

        if ($modelForm->validate()) {
            $redirectId = $this->saveModel($modelForm);
        } else {
            $this->flashService->setError('ADMIN_ITEM_NOT_SAVED');
            $redirectId = $itemId;
        }

        $this->redirect(
            $this->urlService->getBaseUri().'admin/'.$this->routerService->getModuleName(
            ).'/'.$this->routerService->getControllerName().'/edit/'.$redirectId
        );
    }

    private function saveModel(AdminModelFormInterface $modelForm): string
    {
        $model = $modelForm->getEntity();
        $this->eventsManager->fire(self::class.':beforeSaveModel', $model);
        $model->save();
        $this->logService->write($model->getId(), get_class($model), 'Item saved');
        $this->flashService->setSucces('ADMIN_ITEM_SAVED');

        return (string) $model->getId();
    }
}