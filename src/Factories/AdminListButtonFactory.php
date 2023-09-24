<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Factories;

use VitesseCms\Admin\Models\AdminListButton;

class AdminListButtonFactory
{
    public static function create(
        string $cssClass,
        string $href,
        string $title
    ): AdminListButton {
        $adminListButton = new AdminListButton();
        $adminListButton->cssClass = $cssClass;
        $adminListButton->title = $title;
        $adminListButton->href = $href;

        return $adminListButton;
    }
}