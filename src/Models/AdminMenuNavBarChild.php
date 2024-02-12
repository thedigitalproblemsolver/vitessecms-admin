<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

final class AdminMenuNavBarChild
{
    public function __construct(
        private readonly string $name,
        private readonly string $slug,
        private readonly string $target = ''
    ) {
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'target' => $this->target,
        ];
    }
}
