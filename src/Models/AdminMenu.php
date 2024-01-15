<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

final class AdminMenu
{
    private array $navbarItems = [];

    public function __construct(private readonly AdminMenuGroupIterator $groups)
    {
    }

    public function getNavbarItems(): array
    {
        return $this->navbarItems;
    }

    public function addDropdown(string $name, AdminMenuNavBarChildren $children): AdminMenu
    {
        if (!isset($this->navbarItems[$name])) {
            $this->navbarItems[$name] = (new AdminMenuNavBarItem(
                $name,
                '#',
                'dropdown-toggle',
                'data-toggle="dropdown"',
                $children->getItems()
            ))->toArray();
        } else {
            $this->navbarItems[$name]['children'] += $children->getItems();
            ksort($this->navbarItems[$name]['children']);
        }
        
        return $this;
    }

    public function getGroups(): AdminMenuGroupIterator
    {
        return $this->groups;
    }

    public function getGroup(string $group): ?AdminMenuGroup
    {
        return $this->groups->getByKey($group);
    }
}
