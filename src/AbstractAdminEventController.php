<?php declare(strict_types=1);

namespace VitesseCms\Admin;

use VitesseCms\Admin\Traits\TraitAdminControllerFunctions;
use VitesseCms\Core\AbstractEventController;

abstract class AbstractAdminEventController extends AbstractEventController
{
    use TraitAdminControllerFunctions;
}
