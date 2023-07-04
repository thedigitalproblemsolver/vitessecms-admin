<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Core\Utils\StringUtil;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelReadOnly
{
    protected bool $isReadOnly = true;

    public function readOnlyAction(string $id): void
    {
        $model = $this->getModel($id);
        if($model !== null) {
            $properties = get_class_vars($model::class);
            unset(
                $properties['findValue'],
                $properties['deletedOn'],
                $properties['published'],
                $properties['parentId'],
                $properties['hasChildren'],
                $properties['slug'],
                $properties['name'],
                $properties['createdAt'],
                $properties['updatedOn']
            );
            ksort($properties);
            $vars = [];
            foreach($properties as $key => $property) {
                if(isset($model->$key)) {
                    $value = $model->$key;
                    if (gettype($value) === 'object') {
                        if ($value::class === 'MongoDB\BSON\UTCDateTime') {
                            $value = $value->toDateTime()->format('Y-m-d H:i:s');
                        }
                    }
                    switch ($key) {
                        case 'itemId':
                            /*var_dump((string) $value);
                            $item = $this->itemRepository->getById((string) $value, false);
                            var_dump($item);
                            die();
                            if($item !== null) {
                                $value = $item->getNameField();
                            }*/
                            break;
                        case 'userId':
                            $user = $this->userRepository->getById((string) $value, false);
                            if($user !== null) {
                                $value = $user->getNameField();
                            }
                            break;
                    }


                    $vars[] = [
                        'key' => ucfirst(StringUtil::camelCaseToSeperator($key)),
                        'value' => $value
                    ];
                }
            }
            $this->viewService->setVar('content',
                $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
                    'adminModelReadOnly',
                    '',
                    [
                        'model' => $model,
                        'properties' => $vars
                    ]
            )));
        }
    }
}