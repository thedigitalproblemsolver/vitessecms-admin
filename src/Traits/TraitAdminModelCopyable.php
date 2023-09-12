<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Language\Enums\LanguageEnum;
use VitesseCms\Language\Repositories\LanguageRepository;

trait TraitAdminModelCopyable
{
    protected bool $isCopyable = true;

    public function copyAction(string $id): void
    {
        /** @var LanguageRepository $languageRepository */
        $languageRepository = $this->eventsManager->fire(LanguageEnum::GET_REPOSITORY->value, new \stdClass());
        $model = $this->getModel($id);

        $model->resetId();
        $model->set('createdAt', date('Y-m-d H:i:s'));
        $model->setPublished(false);

        $languages = $languageRepository->findAll();
        while ($languages->valid()) {
            $language = $languages->current();
            $model->set(
                'name',
                $model->getNameField($language->getShortCode()) . ' - copy',
                true,
                $language->getShortCode()
            );
            $languages->next();
        }
        $model->save();

        $this->redirect($this->urlService->getBaseUri().'admin/' . $this->routerService->getModuleName() . '/' . $this->routerService->getControllerName() . '/adminList/');
    }
}