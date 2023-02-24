<?php declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Media\Services\AssetsService;

class RenderListener
{
    public function __construct(private readonly AssetsService $assetsService, private bool $isAdmin){}

    public function loadAssets(Event $event): void
    {
        $this->assetsService->loadAdmin();
    }
}