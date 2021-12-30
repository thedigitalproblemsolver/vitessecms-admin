<?php declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use VitesseCms\Admin\Listeners\Admin\AdminAssetsListener;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Media\Enums\MediaEnum;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach(
            MediaEnum::ASSETS_LISTENER,
            new AdminAssetsListener($di->configuration->getVendorNameDir())
        );
    }
}
