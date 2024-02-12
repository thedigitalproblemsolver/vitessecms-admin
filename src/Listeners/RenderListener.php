<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use VitesseCms\Media\Services\AssetsService;

final class RenderListener
{
    public function __construct(
        private readonly bool $isAdminPage,
        private readonly bool $hasAdminAccess,
        private readonly AssetsService $assetsService
    ) {
    }

    public function loadAssets(): void
    {
        if ($this->isAdminPage || $this->hasAdminAccess) {
            $this->assetsService->loadAdmin();
        }
    }
}
