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
                    var_dump($key);
                    var_dump($model->$key);
                    $vars[] = [
                        'key' => ucfirst(StringUtil::camelCaseToSeperator($key)),
                        'value' => $this->getReadOnlyValue($key, $model->class,$model->$key)
                    ];
                }
            }

            $this->viewService->setVar('content',
                $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT,new RenderTemplateDTO(
                    'adminModelReadOnly',
                    '',
                    ['model' => $model, 'properties' => $vars]
                )));
        }
    }

    private function getReadOnlyValue(string $key, ?string $class, string|object $value):string
    {
        if (gettype($value) === 'object') {
            if ($value::class === 'MongoDB\BSON\UTCDateTime') {
                $value = $value->toDateTime()->format('Y-m-d H:i:s');
            }
        }

        switch ($key) {
            case 'itemId':
                if(!empty($class)) {
                    $eventTrigger = array_reverse(explode('\\',$class))[0].'Listener:getRepository';
                    $repository = $this->eventsManager->fire($eventTrigger, new \stdClass());
                    if($repository !== null) {
                        $item = $repository->getById((string)$value, false);
                        if ($item !== null) {
                            $value = $item->getNameField().' ( '.$value.' )';
                        }
                    }
                }
                break;
            case 'userId':
                $user = $this->userRepository->getById((string) $value, false);
                if($user !== null) {
                    $value = $user->getNameField().' ( '.$value.' )';
                }
                break;
        }

        return (string)$value;
    }
}