<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

final class AdminMenuNavBarChildren
{
    /**
     * @var array<int|string,array<string,string>>
     */
    private array $items = [];

    public function addChild(string $name, string $slug, string $target = ''): AdminMenuNavBarChildren
    {
        $this->items[$name] = (new AdminMenuNavBarChild($name, $slug, $target))->toArray();

        return $this;
    }

    public function addLine(): AdminMenuNavBarChildren
    {
        $this->items[] = (new AdminMenuNavBarChild('<hr/>', '#'))->toArray();

        return $this;
    }

    /**
     * @return array<int|string,array<string,string>>
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
