<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use Phalcon\Incubator\MongoDB\Mvc\Collection\Exception;
use VitesseCms\Language\Enums\LanguageEnum;

trait TraitAdminModelDeletable
{
    protected bool $isDeletable = true;

    /**
     * @throws Exception
     */
    public function deleteAction(string $itemId): void
    {
        $languageService = $this->eventsManager->fire(LanguageEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());
        $model = $this->getModel($itemId);
        if (null !== $model) {
            if (false !== $this->eventsManager->fire(self::class.':validateDeleteAction', $model)) {
                $model->delete();
                $this->logService->write(
                    $model->getId(),
                    get_class($model),
                    $languageService->get('ADMIN_ITEM_DELETED', [$model->getNameField()])
                );
                $this->flashService->setSucces('ADMIN_ITEM_DELETED', [$model->getNameField()]);
            }
        } else {
            $this->flashService->setError('ADMIN_ITEM_NOT_FOUND');
        }

        $this->redirect($this->request->getHTTPReferer());
    }
}
