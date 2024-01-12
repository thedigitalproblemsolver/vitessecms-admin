<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Tests\Models;

use PHPUnit\Framework\TestCase;
use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuGroup;
use VitesseCms\Admin\Models\AdminMenuGroupIterator;
use VitesseCms\Core\Enum\SystemEnum;
use VitesseCms\Datagroup\Models\DatagroupIterator;

class AdminMenuTest extends TestCase
{
    public function testClassConstructor()
    {
        $adminGroupIterator = new AdminMenuGroupIterator();
        foreach (SystemEnum::COMPONENTS as $key => $label) :
            $adminGroupIterator->add(
                new AdminMenuGroup(
                    $label,
                    $key,
                    new DatagroupIterator()
                )
            );
        endforeach;

        $adminMenu = new AdminMenu($adminGroupIterator);

        $this->assertSame(6, $adminMenu->getGroups()->count());
    }
}