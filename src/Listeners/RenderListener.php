<?php declare(strict_types=1);

namespace VitesseCms\Admin\Listeners;

use VitesseCms\Media\Services\AssetsService;

class RenderListener {
    public function __construct(
        private readonly bool $isAdminPage,
        private readonly AssetsService $assetsService
    ){}

    public function loadAssets(): void
    {
        if($this->isAdminPage) {
            $this->assetsService->loadAdmin();
        }
    }
}