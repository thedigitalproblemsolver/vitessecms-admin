<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Utils;

use Phalcon\Events\Manager;
use VitesseCms\Admin\Forms\AdminToolbarForm;
use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuGroup;
use VitesseCms\Admin\Models\AdminMenuGroupIterator;
use VitesseCms\Core\Enum\SystemEnum;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Models\User;
use VitesseCms\User\Utils\PermissionUtils;

class AdminUtil
{
    public function __construct(
        private readonly User $user,
        private readonly Manager $eventsManager,
        private readonly DatagroupRepository $datagroupRepository
    ) {
    }

    public static function isAdminPage(): bool
    {
        return !(0 === substr_count($_SERVER['REQUEST_URI'] ?? '', 'admin/'));
    }

    /**
     * @return array<mixed>
     */
    public function getToolbar(): array
    {
        $adminGroupIterator = new AdminMenuGroupIterator();
        foreach (SystemEnum::COMPONENTS as $key => $label) {
            $datagroupIterator = $this->datagroupRepository->getBySystemComponent($key);
            if (null !== $datagroupIterator) {
                $adminGroupIterator->add(new AdminMenuGroup($label, $key, $datagroupIterator));
            }
        }

        $adminMenu = new AdminMenu($adminGroupIterator);
        $this->eventsManager->fire('adminMenu:AddChildren', $adminMenu);
        $adminForm = new AdminToolbarForm();
        $adminForm->setFormClass('form-inline my-2 my-lg-0');

        return [
            'navClass' => 'admin-toolbar fixed-top navbar-dark',
            'items' => $this->toolbarAclCheck($adminMenu->getNavbarItems()),
            'form' => $adminForm->renderForm(
                'admin/core/adminindex/toggleParameters',
                'adminToolbarForm'
            ),
        ];
    }

    /**
     * @param array<mixed> $navbarItems
     * @return array<mixed>
     */
    protected function toolbarAclCheck(array $navbarItems): array
    {
        foreach ($navbarItems as $parentIndex => $parent) {
            foreach ($parent['children'] as $childIndex => $child) {
                if ('#' !== $child['slug']) {
                    $path = explode('/', $child['slug']);
                    if (
                        'superadmin' !== $this->user->getPermissionRole()
                        && !PermissionUtils::check($this->user, $path[1], $path[2], $path[3] ?? '')
                    ) {
                        unset($parent['children'][$childIndex]);
                    }
                }
            }

            if (0 === count($parent['children'])) {
                unset($navbarItems[$parentIndex]);
            } else {
                $navbarItems[$parentIndex]['children'] = array_values($parent['children']);
            }
        }

        return array_values($navbarItems);
    }
}
