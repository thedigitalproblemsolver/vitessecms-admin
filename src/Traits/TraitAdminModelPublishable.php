<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

trait TraitAdminModelPublishable
{
    protected bool $isPublishable = true;

    public function togglePublishAction(string $itemId): void
    {
        $model = $this->getModel($itemId);
        switch ($model->isPublished()) {
            case false:
                $model->setPublished(true);
                $this->flashService->setSucces('ADMIN_ITEM_PUBLISHED');
                $this->logService->write($model->getId(), $model::class, '%ADMIN_ITEM_PUBLISHED%');
                break;
            case true:
                $model->setPublished(false);
                $this->flashService->setSucces('ADMIN_ITEM_UNPUBLISHED');
                $this->logService->write($model->getId(), $model::class, '%ADMIN_ITEM_UNPUBLISHED%');
                break;
        }
        $model->save();

        $this->redirect(
            $this->urlService->getBaseUri().'admin/'.$this->routerService->getModuleName(
            ).'/'.$this->routerService->getControllerName().'/adminlist/'
        );
    }
}