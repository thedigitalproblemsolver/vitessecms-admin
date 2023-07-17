<?php declare(strict_types=1);

namespace VitesseCms\Admin\Blocks;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class Toolbar extends AbstractBlockModel
{
    public function getTemplateParams(Block $block): array
    {
        $params = parent::getTemplateParams($block);
        $params['toolbar'] = $this->view->renderTemplate(
            'navbar',
            'partials',
            ['navbar' => (new AdminUtil(
                $this->getDi()->get('user'),
                $this->getDi()->get('eventsManager'),
                new DatagroupRepository()
            ))->getToolbar()
            ]
        );

        return $params;
    }
}