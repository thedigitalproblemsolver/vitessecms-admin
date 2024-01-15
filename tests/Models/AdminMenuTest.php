<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Tests\Models;

use PHPUnit\Framework\TestCase;
use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuGroup;
use VitesseCms\Admin\Models\AdminMenuGroupIterator;
use VitesseCms\Admin\Models\AdminMenuNavBarChildren;
use VitesseCms\Core\Enum\SystemEnum;
use VitesseCms\Datagroup\Models\DatagroupIterator;

class AdminMenuTest extends TestCase
{
    public function testClassConstructor(): void
    {
        $this->assertSame(6, $this->createClass()->getGroups()->count());
    }

    public function testGetGroup(): void
    {
        $this->assertSame('content',$this->createClass()->getGroup('content')->getKey());
    }

    public function testAddDropdown(): void
    {
        $dropdown = new AdminMenuNavBarChildren();
        $dropdown->addChild('item 1','item_1/','_blank');

        $adminMenu = $this->createClass();
        $adminMenu->addDropdown('test dropdown', $dropdown);

        $navBarItems = $adminMenu->getNavbarItems();

        $this->assertIsArray($navBarItems);
        $this->assertIsArray($navBarItems['test dropdown']);
        $this->assertSame(5,count($navBarItems['test dropdown']));
        $this->assertIsArray($navBarItems['test dropdown']['children']);
        $this->assertSame(['item 1' => [
                'name' => 'item 1',
                'slug' => 'item_1/',
                'target' => '_blank',
        ]],$navBarItems['test dropdown']['children']);
        $this->assertIsArray($navBarItems['test dropdown']['children']['item 1']);
        $this->assertSame(
            ['name' => 'item 1', 'slug' => 'item_1/', 'target' => '_blank'],
            $navBarItems['test dropdown']['children']['item 1']
        );
    }

    private function createClass():AdminMenu
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

        return new AdminMenu($adminGroupIterator);
    }
}