<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use stdClass;
use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Admin\Helpers\PaginationHelper;
use VitesseCms\Admin\Models\AdminListButtonIterator;
use VitesseCms\Database\Enums\FindValueTypeEnum;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;
use VitesseCms\User\Enum\AclEnum;

trait TraitAdminModelList
{
    public function adminListAction(): void
    {
        $aclService = $this->eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $adminlistForm = new AdminlistForm();
        $controllerUri = $this->urlService->getBaseUri().'admin/'.$this->router->getModuleName(
            ).'/'.$this->router->getControllerName();

        $paginationHelper = new PaginationHelper(
            $this->getModelList($this->getFilterValues()),
            $this->urlService,
            $this->request->get('offset', 'int', 0)
        );

        $this->eventsManager->fire(get_class($this).':adminListFilter', $this, $adminlistForm);

        $buttons = new AdminListButtonIterator([]);
        $this->eventsManager->fire(get_class($this).':adminListButtons', $buttons);

        $renderedModelList = $this->eventsManager->fire(
            ViewEnum::RENDER_TEMPLATE_EVENT,
            new RenderTemplateDTO(
                $this->adminListWithPaginationTemplate(),
                '',
                [
                    'pagination' => $paginationHelper,
                    'actionBaseUri' => $controllerUri,
                    'baseUri' => $this->url->getBaseUri(),
                    'canPreview' => $aclService->hasAccess('preview') && ($this->isPreviewable ?? false),
                    'canDelete' => $aclService->hasAccess('delete') && ($this->isDeletable ?? false),
                    'canEdit' => $aclService->hasAccess('edit') && ($this->isEditable ?? false),
                    'canPublish' => $aclService->hasAccess('togglepublish') && ($this->isPublishable ?? false),
                    'canReadonly' => $aclService->hasAccess('readonly') && ($this->isReadOnly ?? false),
                    'canCopy' => $aclService->hasAccess('copy') && ($this->isCopyable ?? false),
                ]
            )
        );

        $this->viewService->set(
            'content',
            $this->eventsManager->fire(
                ViewEnum::RENDER_TEMPLATE_EVENT,
                new RenderTemplateDTO(
                    $this->request->isAjax() ? 'adminModelListWithoutFilter' : 'adminModelListWithFilter',
                    '',
                    [
                        'actionBaseUri' => $controllerUri,
                        'canAdd' => $aclService->hasAccess('add') && ($this->isAddable ?? false),
                        'buttons' => $buttons,
                        'filterForm' => $adminlistForm->renderForm($controllerUri.'/adminlist', 'adminFilter'),
                        'list' => $renderedModelList,
                    ]
                )
            )
        );
    }

    protected function getFilterValues(): ?FindValueIterator
    {
        $filter = $this->getFilter();

        if ($filter === null) {
            return null;
        }

        $filterValues = [];
        foreach ($filter as $key => $filterInput) {
            if (is_string($filterInput)) {
                $value = trim($filterInput);
            }

            if (!empty($filterInput)) {
                switch ($key) {
                    case 'published':
                        $filterValues[] = match ($filterInput) {
                            'true' => new FindValue($key, true),
                            'false' => new FindValue($key, false)
                        };
                        break;
                    case 'date':
                        $fieldName = array_key_first($filterInput);
                        if (!empty($filterInput[$fieldName]['from'])) {
                            $filterValues[] = new FindValue(
                                $fieldName,
                                $filterInput[$fieldName]['from'],
                                FindValueTypeEnum::GREATER_THAN->value
                            );
                        }
                        if (!empty($filterInput[$fieldName]['till'])) {
                            $filterValues[] = new FindValue(
                                $fieldName,
                                $filterInput[$fieldName]['till'],
                                FindValueTypeEnum::SMALLER_THAN->value
                            );
                        }
                        break;
                    default:
                        $filterValues[] = new FindValue($key, $value, FindValueTypeEnum::LIKE->value);
                        break;
                }
            }
        }

        return new FindValueIterator($filterValues);
    }

    private function getFilter(): ?array
    {
        $sessionKey = 'filter_'.md5($this::class);

        if (!$this->request->hasPost('filter') && !$this->session->has($sessionKey)) {
            return null;
        }

        if (!$this->request->hasPost('filter') && $this->session->has($sessionKey)) {
            $_REQUEST['filter'] = $this->session->get($sessionKey);

            return $_REQUEST['filter'];
        }

        $this->session->set($sessionKey, $this->request->getPost('filter'));

        return $this->request->getPost('filter');
    }

    protected function adminListWithPaginationTemplate(): string
    {
        return 'adminModelListWithPagination';
    }
}