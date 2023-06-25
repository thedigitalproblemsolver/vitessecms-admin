<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Analytics\Models\BlackListEntry;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;
use VitesseCms\User\Enum\AclEnum;

trait TraitAdminModelSave
{
    public function saveAction(string $id): void
    {
        $modelForm = $this->getModelForm();
        $modelForm->buildForm();
        if($id === 'new') {
            $modelForm->setEntity(new BlackListEntry());
        } else {
            $modelForm->setEntity($this->getModel($id));
        }

        $modelForm->bind($this->request->getPost());
        if($modelForm->validate()) {
            $model = $modelForm->getEntity();
            $model->save();
            $this->logService->write($model->getId(), get_class($model), 'Item saved');
            $this->flashService->setSucces('ADMIN_ITEM_SAVED');
        }

        $this->redirect($this->urlService->getBaseUri().'admin/' . $this->routerService->getModuleName() . '/' . $this->routerService->getControllerName() . '/edit/'.$model->getId());
    }
}