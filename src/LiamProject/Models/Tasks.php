<?php

namespace LiamProject\Models;

use LiamProject\Exceptions\TaskNotFoundException;

class Tasks extends AmocrmEntity
{
    protected function setEntityName(): string
    {
        return 'tasks';
    }

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


        $out = $this->queryToAmo($api, 'POST', $data);

//        var_dump($out);
    }

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