<?php declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach('RenderListener', new RenderListener(
            $di->assets,
            AdminUtil::isAdminPage() || $di->user->hasAdminAccess()
        ));
    }
}
