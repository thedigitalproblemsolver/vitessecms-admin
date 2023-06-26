<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Language\Enums\LanguageEnum;
use VitesseCms\User\Enum\AclEnum;

trait TraitAdminModelDeletable
{
    protected bool $isDeletable = true;

    public function deleteAction(string $id): void
    {
        $languageService = $this->eventsManager->fire(LanguageEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());
        $model = $this->getModel($id);
        if($model !== null) {
            $model->delete();
            $this->logService->write(
                $model->getId(),
                get_class($model),
                $languageService->get('ADMIN_ITEM_DELETED', [$model->getNameField()])
            );
            $this->flashService->setSucces('ADMIN_ITEM_DELETED', [$model->getNameField()]);
        } else {
            $this->flashService->setError('ADMIN_ITEM_NOT_FOUND');
        }

        $this->redirect($this->request->getHTTPReferer());
    }
}