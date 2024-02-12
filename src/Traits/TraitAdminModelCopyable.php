<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use Phalcon\Incubator\MongoDB\Mvc\Collection\Exception;
use VitesseCms\Language\Enums\LanguageEnum;

trait TraitAdminModelCopyable
{
    protected bool $isCopyable = true;

    /**
     * @throws Exception
     */
    public function copyAction(string $itemId): void
    {
        $languageRepository = $this->eventsManager->fire(LanguageEnum::GET_REPOSITORY->value, new \stdClass());
        $model = $this->getModel($itemId);

        $model->resetId();
        $model->set('createdAt', date('Y-m-d H:i:s'));
        $model->setPublished(false);

        $languages = $languageRepository->findAll();
        while ($languages->valid()) {
            $language = $languages->current();
            $model->set(
                'name',
                $model->getNameField($language->getShortCode()).' - copy',
                true,
                $language->getShortCode()
            );
            $languages->next();
        }
        $model->save();

        $this->redirect(
            $this->urlService->getBaseUri().'admin/'.$this->routerService->getModuleName(
            ).'/'.$this->routerService->getControllerName().'/adminList/'
        );
    }
}
