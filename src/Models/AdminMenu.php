<?php declare(strict_types=1);

namespace VitesseCms\Admin\Models;

class AdminMenu
{
    /**
     * @var array
     */
    protected $navbarItems;

    /**
     * @var AdminMenuGroupIterator
     */
    protected $groups;

    public function __construct( array $navbarItems, AdminMenuGroupIterator $groups)
    {
        $this->navbarItems = $navbarItems;
        $this->groups = $groups;
    }

    public function getNavbarItems(): array
    {
        return $this->navbarItems;
    }

    public function addDropdown(string $name, AdminMenuNavBarChildren $children): AdminMenu
    {
        if (!isset($this->navbarItems[$name])) :
            $this->navbarItems[$name] = (new AdminMenuNavBarItem(
                $name,
                '#',
                'dropdown-toggle',
                'data-toggle="dropdown"',
                $children->getItems()
            ))->toArray();
        else :
            $this->navbarItems[$name]['children'] += $children->getItems();
            ksort($this->navbarItems[$name]['children']);
        endif;

        return $this;
    }

    public function getGroups(): AdminMenuGroupIterator
    {
        return $this->groups;
    }
}
