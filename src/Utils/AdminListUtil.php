<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Utils;

use Phalcon\Events\Manager;
use Phalcon\Tag;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Utils\MongoUtil;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Enum\AclEnum;
use VitesseCms\User\Services\AclService;

class AdminListUtil
{
    public static function getAdminListButtons(
        AbstractCollection $item,
        string $editBaseUri,
        Manager $eventsManager
    ): string {
        $return = '';
        /** @var AclService $aclService */
        $aclService = $eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());
        /** @var DatagroupRepository $datagroupRepository */
        $datagroupRepository = $eventsManager->fire(DatagroupEnum::GET_REPOSITORY->value, new \stdClass());

        if ($item->hasSlug()) {
            $return = Tag::linkTo([
                'action' => $item->getString('slug'),
                'target' => '_blank',
                'title' => 'Preview',
                'class' => 'fa fa-eye',
            ]);
        }

        if ($aclService->hasAccess(AclEnum::ACCESS_TOGGLE_PUBLISH->value)) {
            $return .= Tag::linkTo([
                'action' => $editBaseUri.'/togglePublish/'.$item->getId(),
                'class' => ItemHelper::getPublishIcon($item->isPublished()),
                'title' => ItemHelper::getPublishText($item->isPublished()),
                'id' => 'publish_'.$item->getId(),
            ]);
        }

        if ($aclService->hasAccess(AclEnum::ACCESS_EDIT->value)) {
            $showAddChild = false;

            $datagroupId = $item->getString('datagroup');
            if (MongoUtil::isObjectId($datagroupId)) {
                $datagroup = $datagroupRepository->getById($datagroupId, false);
                if (null !== $datagroup) {
                    $showAddChild = $datagroup->hasChildren();
                }
            }

            if ($showAddChild) {
                $return .= Tag::linkTo([
                    'action' => $editBaseUri.'/edit/?parentId='.$item->getId(),
                    'class' => 'fa fa-plus openmodal',
                    'title' => '%ADMIN_ADD_CHILD_ITEM%',
                ]);
            }

            $return .= Tag::linkTo([
                'action' => $editBaseUri.'/edit/'.$item->getId(),
                'class' => 'fa fa-edit openmodal',
                'title' => '%ADMIN_EDIT_ITEM%',
            ]);
        }

        if ($aclService->hasAccess(AclEnum::ACCESS_COPY->value)) {
            $return .= Tag::linkTo([
                'action' => $editBaseUri.'/copy/'.$item->getId(),
                'class' => 'fa fa-copy',
                'title' => '%ADMIN_COPY_ITEM%',
            ]);
        }

        if ($aclService->hasAccess(AclEnum::ACCESS_DELETE->value)) {
            $return .= Tag::linkTo([
                'action' => $editBaseUri.'/delete/'.$item->getId(),
                'class' => 'fa fa-trash',
                'title' => '%ADMIN_DELETE_ITEM%',
                'id' => 'delete_'.$item->getId(),
            ]);
        }

        return $return;
    }
}
