<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Tests\Forms;

use Phalcon\Di\Di;
use Phalcon\Events\Manager;
use PHPUnit\Framework\TestCase;
use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Core\Services\BootstrapService;
use VitesseCms\Language\Enums\LanguageEnum;
use VitesseCms\Language\Listeners\LanguageListener;
use VitesseCms\Language\Repositories\LanguageRepository;
use VitesseCms\Language\Services\LanguageService;

final class AdminlistFormTest extends TestCase
{
    /**
     * @todo add when form is not dependen on di
     */
    public function testAddedFields(){
    }
}