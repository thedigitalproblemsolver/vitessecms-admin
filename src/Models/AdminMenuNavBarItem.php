<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

final class AdminMenuNavBarItem
{
    /**
     * @param array<int,array<string,string>> $children
     */
    public function __construct(
        private readonly string $name,
        private readonly string $slug,
        private readonly string $class,
        private readonly string $extra,
        private readonly array $children
    ) {
    }

    /**
     * @return array<string,string|array<int,array<string,string>>>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'class' => $this->class,
            'extra' => $this->extra,
            'children' => $this->children,
        ];
    }
}
