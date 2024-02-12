<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Media\Enums\AssetsEnum;

final class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        $injectable->eventsManager->attach(
            AssetsEnum::RENDER_LISTENER->value,
            new RenderListener(
                AdminUtil::isAdminPage(),
                $injectable->user->hasAdminAccess(),
                $injectable->assets
            )
        );
    }
}
