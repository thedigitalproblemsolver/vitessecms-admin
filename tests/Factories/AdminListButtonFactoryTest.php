<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Tests\Factories;

use PHPUnit\Framework\TestCase;
use VitesseCms\Admin\Factories\AdminListButtonFactory;
use VitesseCms\Admin\Models\AdminListButton;

final class AdminListButtonFactoryTest extends TestCase
{
    public function testCreate()
    {
        $adminListButton = AdminListButtonFactory::create('openmodal','http://test.nl','Test website');
        $this->assertInstanceOf(AdminListButton::class, $adminListButton);
        $this->assertSame('openmodal',$adminListButton->cssClass);
        $this->assertSame('http://test.nl',$adminListButton->href);
        $this->assertSame('Test website',$adminListButton->title);
    }

}