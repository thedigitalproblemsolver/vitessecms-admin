<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Blocks;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

class Toolbar extends AbstractBlockModel
{
    /**
     * @return array<mixed>
     */
    public function getTemplateParams(Block $block): array
    {
        $params = parent::getTemplateParams($block);
        $params['toolbar'] = $this->di->get('eventsManager')->fire(
            ViewEnum::RENDER_PARTIAL_EVENT,
            new RenderPartialDTO(
                'navbar',
                [
                    'navbar' => (new AdminUtil(
                        $this->di->get('user'),
                        $this->di->get('eventsManager'),
                        new DatagroupRepository()
                    ))->getToolbar(),
                ]
            )
        );

        return $params;
    }
}
