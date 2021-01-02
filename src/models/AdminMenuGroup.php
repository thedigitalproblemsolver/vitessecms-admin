<?php declare(strict_types=1);

namespace VitesseCms\Admin\Models;

use VitesseCms\Core\Models\Datagroup;
use VitesseCms\Core\Models\DatagroupIterator;

class AdminMenuGroup
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var DatagroupIterator
     */
    protected $datagroups;

    public function __construct(string $label, string $key, DatagroupIterator $datagroups)
    {
        $this->label = $label;
        $this->key = $key;
        $this->datagroups = $datagroups;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDatagroups(): DatagroupIterator
    {
        return $this->datagroups;
    }
}
