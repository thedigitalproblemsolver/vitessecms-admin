<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\User\Enum\AclEnum;

trait TraitAdminModelList
{
    public function adminListAction(): void
    {
        $link = $this->url->getBaseUri() . 'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName();
        $listTemplate = 'adminModelList';
        $listTemplatePath = $this->configService->getVendorNameDir() . 'admin/src/Resources/views/';
        $aclService = $this->eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());

        $this->viewService->set(
            'content',
            $this->viewService->renderTemplate(
                $listTemplate,
                $listTemplatePath,
                [
                    'models' => $this->getModelList(),
                    'editBaseUri' => $link,
                    'canDelete' => $aclService->hasAccess('delete')
                    //'isAjax' => $this->request->isAjax(),
                    /*'filter' => $this->eventsManager->fire(
                        get_class($this) . ':adminListFilter',
                        $this,
                        new AdminlistForm()
                    ),*/
                    //'adminListButtons' => $adminListButtons,
                    //'displayEditButton' => $displayEditButton
                ]
            )
        );
    }
}