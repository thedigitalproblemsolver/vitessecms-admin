<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;
use VitesseCms\User\Enum\AclEnum;

trait TraitAdminModelList
{
    public function adminListAction(): void
    {
        $aclService = $this->eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());
        $adminlistForm = new AdminlistForm();
        $controllerUri = $this->url->getBaseUri() . 'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName();
        $this->eventsManager->fire(get_class($this) . ':adminListFilter', $this, $adminlistForm);

        $renderedModelList = $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
            'adminModelList',
            '',
            [
                'models' => $this->getModelList($this->getFindValues()),
                'actionBaseUri' => $controllerUri,
                'baseUri' => $this->url->getBaseUri(),
                'canPreview' => $aclService->hasAccess('preview') && ($this->isPreviewable??false),
                'canDelete' => $aclService->hasAccess('delete') && ($this->isDeletable??false),
                'canEdit' => $aclService->hasAccess('edit') && ($this->isEditable??false),
                'canPublish' => $aclService->hasAccess('togglepublish') && ($this->isPublishable??false),
                'canReadonly'  => $aclService->hasAccess('readonly') && ($this->isReadOnly??false),
            ]
        ));

        $this->viewService->set(
            'content',
            $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
                $this->request->isAjax()?'adminModelListWithoutFilter':'adminModelListWithFilter',
                '',
                [
                    'actionBaseUri' => $controllerUri,
                    'canAdd' => $aclService->hasAccess('add') && ($this->isAddable??false),
                    'filterForm' => $adminlistForm->renderForm($controllerUri.'/adminlist', 'adminFilter'),
                    'list' => $renderedModelList
                ]
            ))
        );
    }

    protected function getFindValues(): ?FindValueIterator
    {
        if(!$this->request->hasPost('filter')) {
            return null;
        }

        $filterValues = [];
        foreach ($this->request->getPost('filter') as $key => $filterInput) {
            if (is_string($filterInput)) :
                $value = trim($filterInput);
            endif;

            if (!empty($filterInput)) {
                $filterValues[] = new FindValue($key, $value, 'like');
            }
        }

        return new FindValueIterator($filterValues);
    }
}