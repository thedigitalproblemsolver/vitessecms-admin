<?php
declare(strict_types=1);

namespace VitesseCms\Admin\Utils;

use Phalcon\Events\Manager;
use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuGroup;
use VitesseCms\Admin\Models\AdminMenuGroupIterator;
use VitesseCms\Core\Enum\SystemEnum;
use VitesseCms\Core\Forms\AdminToolbarForm;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Models\User;
use VitesseCms\User\Utils\PermissionUtils;

class AdminUtil
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Manager
     */
    protected $eventsManager;

    /**
     * @var DatagroupRepository
     */
    protected $datagroupRepository;

    public function __construct(
        User $user,
        Manager $eventsManager,
        DatagroupRepository $datagroupRepository
    ) {
        $this->user = $user;
        $this->eventsManager = $eventsManager;
        $this->datagroupRepository = $datagroupRepository;
    }

    public static function isAdminPage(): bool
    {
        return !(substr_count($_SERVER['REQUEST_URI'] ?? '', 'admin/') === 0);
    }

    public function getToolbar(): array
    {
        $adminGroupIterator = new AdminMenuGroupIterator();
        foreach (SystemEnum::COMPONENTS as $key => $label) :
            $adminGroupIterator->add(
                new AdminMenuGroup(
                    $label,
                    $key,
                    $this->datagroupRepository->getBySystemComponent($key)
                )
            );
        endforeach;

        $adminMenu = new AdminMenu([], $adminGroupIterator);
        $this->eventsManager->fire('adminMenu:AddChildren', $adminMenu);
        $adminForm = new AdminToolbarForm();
        $adminForm->setFormClass('form-inline my-2 my-lg-0');

        return [
            'navClass' => 'admin-toolbar fixed-top navbar-dark',
            'items' => $this->toolbarAclCheck($adminMenu->getNavbarItems()),
            'form' => $adminForm->renderForm(
                'admin/core/adminindex/toggleParameters',
                'adminToolbarForm'
            )
        ];
    }

    protected function toolbarAclCheck(array $navbarItems): array
    {
        foreach ($navbarItems as $parentIndex => $parent) :
            foreach ($parent['children'] as $childIndex => $child) :
                if ($child['slug'] !== '#') :
                    $path = explode('/', $child['slug']);
                    if (
                        'superadmin' !== $this->user->getPermissionRole() &&
                        !PermissionUtils::check($this->user, $path[1], $path[2], $path[3] ?? '')
                    ) :
                        unset($parent['children'][$childIndex]);
                    endif;
                endif;
            endforeach;

            if (count($parent['children']) === 0) :
                unset($navbarItems[$parentIndex]);
            else :
                $navbarItems[$parentIndex]['children'] = array_values($parent['children']);
            endif;
        endforeach;

        return array_values($navbarItems);
    }
}
