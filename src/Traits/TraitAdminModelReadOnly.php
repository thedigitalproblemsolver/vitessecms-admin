<?php declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Core\Utils\StringUtil;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelReadOnly
{
    protected bool $isReadOnly = false;

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
                $value = $model->$key;
                if(gettype($value) === 'object') {
                    if ($value::class === 'MongoDB\BSON\UTCDateTime' ) {
                        $value = $value->toDateTime()->format('Y-m-d H:i:s');
                    }
                }
                $vars[] = [
                    'key' => ucfirst(StringUtil::camelCaseToSeperator($key)),
                    'value' => $value
                ];
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