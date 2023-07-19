<?php declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Media\Enums\AssetsEnum;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach(
            AssetsEnum::RENDER_LISTENER->value,
            new RenderListener(
                AdminUtil::isAdminPage(),
                $di->user->hasAdminAccess(),
                $di->assets
            )
        );
    }
}
