<?php

declare(strict_types=1);

namespace VitesseCms\Admin\Traits;

use VitesseCms\Core\Utils\StringUtil;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

trait TraitAdminModelReadOnly
{
    protected bool $isReadOnly = true;

    public function readOnlyAction(string $itemId): void
    {
        $model = $this->getModel($itemId);

        if (null !== $model) {
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
            foreach ($properties as $key => $property) {
                if (isset($model->$key)) {
                    $vars[] = [
                        'key' => ucfirst(StringUtil::camelCaseToSeperator($key)),
                        'value' => $this->getReadOnlyValue($key, $model),
                    ];
                }
            }

            $this->viewService->setVar(
                'content',
                $this->eventsManager->fire(
                    ViewEnum::RENDER_TEMPLATE_EVENT,
                    new RenderTemplateDTO(
                        'adminModelReadOnly',
                        '',
                        ['model' => $model, 'properties' => $vars]
                    )
                )
            );
        }
    }

    private function getReadOnlyValue(string $key, AbstractCollection $model): string
    {
        $class = $model::class;
        $value = $model->$key;

        if ('object' === gettype($value)) {
            if ('MongoDB\BSON\UTCDateTime' === $value::class) {
                $value = $value->toDateTime()->format('Y-m-d H:i:s');
            }
        }

        return match ($key) {
            'fieldNames' => $this->parseFieldNames($model, $value),
            'itemId' => $this->parseItemId($class, (string) $value),
            'userId' => $this->parseUserId((string) $value),
            default => (string) $value
        };
    }

    private function parseFieldNames(AbstractCollection $model, array $value): string
    {
        $data = [];
        foreach ($value as $k => $v) {
            $data[] = [
                'key' => $v,
                'value' => $model->_($k),
            ];
        }

        if (0 === count($data)) {
            return '';
        }

        return $this->eventsManager->fire(
            ViewEnum::RENDER_TEMPLATE_EVENT,
            new RenderTemplateDTO('FieldNamesTable', '', ['data' => $data])
        );
    }

    private function parseItemId(?string $class, string $value): string
    {
        if (!empty($class)) {
            $eventTrigger = array_reverse(explode('\\', $class))[0].'Listener:getRepository';
            $repository = $this->eventsManager->fire($eventTrigger, new \stdClass());
            if (null !== $repository) {
                $item = $repository->getById($value, false);
                if (null !== $item) {
                    return $item->getNameField().' ( '.$value.' )';
                }
            }
        }

        return $value;
    }

    private function parseUserId(string $value): string
    {
        $user = $this->userRepository->getById($value, false);
        if (null !== $user) {
            return $user->getNameField().' ( '.$value.' )';
        }

        return $value;
    }
}
