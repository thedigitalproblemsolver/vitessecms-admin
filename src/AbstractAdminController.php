<?php

declare(strict_types=1);

namespace VitesseCms\Admin;

use VitesseCms\Admin\Traits\TraitAdminControllerFunctions;
use VitesseCms\Core\AbstractController;

abstract class AbstractAdminController extends AbstractController
{
    use TraitAdminControllerFunctions;
}
