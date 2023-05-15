<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\User\Enum\AclEnum;

trait TraitAdminModelDeletable
{
    public function deleteAction(string $id): void
    {
        $model = $this->getDeletableModel($id);
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

        $this->redirect($this->request->getHTTPReferer());
    }
}