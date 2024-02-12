<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

/**
 * @extends  \ArrayIterator<int, AdminListButton>
 */
final class AdminListButtonIterator extends \ArrayIterator
{
    /**
     * @param array<AdminListButton> $buttons
     */
    public function __construct(array $buttons)
    {
        parent::__construct($buttons);
    }

    public function add(AdminListButton $adminListButton): void
    {
        parent::append($adminListButton);
    }

    public function current(): AdminListButton
    {
        return parent::current();
    }
}
