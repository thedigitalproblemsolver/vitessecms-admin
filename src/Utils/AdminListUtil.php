<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Utils;

use Phalcon\Tag;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Utils\MongoUtil;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\User\Services\AclService;

class AdminListUtil
{
    public static function getAdminListButtons(
        AbstractCollection $item,
        string $editBaseUri,
        AclService $acl
    ): string {
        $return = '';

        if ($item->hasSlug()) {
            $return = Tag::linkTo([
                'action' => $item->_('slug'),
                'target' => '_blank',
                'title' => 'Preview',
                'class' => 'fa fa-eye',
            ]);
        }

        if ($acl->hasAccess('togglepublish')) {
            $return .= Tag::linkTo([
                'action' => $editBaseUri . '/togglePublish/' . $item->getId(),
                'class' => ItemHelper::getPublishIcon($item->isPublished()),
                'title' => ItemHelper::getPublishText($item->isPublished()),
                'id' => 'publish_' . $item->getId(),
            ]);
        }

        if ($acl->hasAccess('edit')) {
            $showAddChild = false;

            $datagroupId = $item->_('datagroup');
            if (MongoUtil::isObjectId($datagroupId)) {
                Datagroup::setFindPublished(false);
                /** @var Datagroup $datagroup */
                $datagroup = Datagroup::findById($datagroupId);
                if ($datagroup) {
                    $showAddChild = $datagroup->hasChildren();
                }
            }

            if ($showAddChild) {
                $return .= Tag::linkTo([
                    'action' => $editBaseUri . '/edit/?parentId=' . $item->getId(),
                    'class' => 'fa fa-plus openmodal',
                    'title' => '%ADMIN_ADD_CHILD_ITEM%',
                ]);
            }

            $return .= Tag::linkTo([
                'action' => $editBaseUri . '/edit/' . $item->getId(),
                'class' => 'fa fa-edit openmodal',
                'title' => '%ADMIN_EDIT_ITEM%',
            ]);
        }

        if ($acl->hasAccess('copy')) {
            $return .= Tag::linkTo([
                'action' => $editBaseUri . '/copy/' . $item->getId(),
                'class' => 'fa fa-copy',
                'title' => '%ADMIN_COPY_ITEM%',
            ]);
        }

        if ($acl->hasAccess('delete')) {
            $return .= Tag::linkTo([
                'action' => $editBaseUri . '/delete/' . $item->getId(),
                'class' => 'fa fa-trash',
                'title' => '%ADMIN_DELETE_ITEM%',
                'id' => 'delete_' . $item->getId(),
            ]);
        }

        return $return;
    }
}
