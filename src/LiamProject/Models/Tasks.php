<?php

namespace LiamProject\Models;

use LiamProject\Exceptions\TaskNotFoundException;
use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Tasks
 * @package LiamProject\Models
 */
class Tasks extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'tasks';
    }

    /**
     * @param $completeTill
     * @param $taskText
     * @param $responsibleUserId
     * @param AmocrmEntity $entity
     * @throws UnauthorizedException
     */
    public function addTaskToEntity($completeTill, $taskText, $responsibleUserId, AmocrmEntity $entity)
    {
        $api = '/api/v4/tasks';
        $entityId = $entity->foundedElement['id'];
        $entityType = $entity->entityName;

        $data[] = [
            "task_type_id" => 1,
            "text" => $taskText,
            "complete_till" => strtotime($completeTill),
            "entity_id" => $entityId,
            "entity_type" => $entityType,
        ];

        if ($responsibleUserId != '') {
            $data['responsible_user_id'] = $responsibleUserId;
        }

        $this->queryToAmo($api, 'POST', $data);
    }

    /**
     * @param $id
     * @throws TaskNotFoundException
     * @throws UnauthorizedException
     */
    public function completeTaskById($id)
    {
        $api = "/api/v4/tasks/$id";

        $data = [
            "is_completed" => true,
            "result" => [
                "text" => "Удалось связаться с клиентом"
            ]
        ];

        $response = $this->queryToAmo($api, 'PATCH', $data);
        if (isset($response['detail']) && $response['detail'] == 'Error 282.') {
            throw new TaskNotFoundException('Задача с таким id не существует');
        }
    }
}