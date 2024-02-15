<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\User\Utils\PermissionUtils;

final class AdminToolbarForm extends AbstractForm
{
    public function initialize(): void
    {
        if (PermissionUtils::check($this->user, 'block', 'adminblockposition', 'edit')) {
            $this->addToggle(
                'Layout',
                'layoutMode',
                (new Attributes())->setChecked($this->session->get('layoutMode', false))
            );
        }
        if (PermissionUtils::check($this->user, 'block', 'adminblock', 'edit')) {
            $this->addToggle(
                'Editor',
                'editorMode',
                (new Attributes())->setChecked($this->session->get('editorMode', false))
            );
        }

        $this->addToggle(
            'Cache',
            'cache',
            (new Attributes())->setChecked(
                $this->session->get('cache', true)
            )
        );

        if (null !== $this->view->getCurrentId()) {
            $javascript = file_get_contents(__DIR__.'/../Resources/react/editButton.jsx');
            if (is_string($javascript)) {
                $this->assets->addInlineBabel($javascript);
                $this->addHtml('<div id="react-edit-button" data-itemid="'.$this->view->getCurrentId().'" ></div>');
            }
        }
    }
}
