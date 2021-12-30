<?php declare(strict_types=1);

namespace VitesseCms\Admin\Listeners\Admin;

use Phalcon\Events\Event;
use VitesseCms\Media\Services\AssetsService;

class AdminAssetsListener
{
    /**
     * @var string
     */
    private $vendorBaseDir;

    public function __construct(string $vendorBaseDir)
    {
        $this->vendorBaseDir = $vendorBaseDir;
    }

    public function initStart(Event $event, AssetsService $assetsService): void
    {
        $assetsService->addInlineJs(file_get_contents($this->vendorBaseDir . 'admin/src/Resources/js/adminInitStart.js'));
    }

    public function initEnd(Event $event, AssetsService $assetsService): void
    {
        $assetsService->addInlineJs(file_get_contents($this->vendorBaseDir . 'admin/src/Resources/js/adminInitEnd.js'));
    }
}