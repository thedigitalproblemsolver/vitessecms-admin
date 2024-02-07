<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Models;

/**
 * @extends \ArrayIterator<int, AdminMenuGroup>
 */
final class AdminMenuGroupIterator extends \ArrayIterator
{
    /**
     * @var array<string,int>
     */
    protected array $index;

    /**
     * @param array<mixed> $groups
     */
    public function __construct(array $groups = [])
    {
        $this->index = [];
        parent::__construct($groups);
    }

    public function add(AdminMenuGroup $adminMenuGroup): AdminMenuGroupIterator
    {
        $this->index[$adminMenuGroup->getKey()] = count($this->index);
        $this->append($adminMenuGroup);

        return $this;
    }

    public function getByKey(string $key): ?AdminMenuGroup
    {
        $this->seek($this->index[$key]);

        if ($this->valid()) {
            return $this->current();
        }

        return null;
    }

    public function current(): AdminMenuGroup
    {
        return parent::current();
    }
}
