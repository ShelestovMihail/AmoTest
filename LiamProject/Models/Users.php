<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UserNotFoundException;

class Users extends AmocrmEntity
{
    protected function setEntityName(): string
    {
        return 'users';
    }

    public function getUserIdById($id): ?int
    {
        $api = "/api/v4/users/$id";

        $response = $this->queryToAmo($api);

        if(isset($response['status']) && $response['status'] == 404) {
            throw new UserNotFoundException("Пользователя с таким id не существует");
        }

        return $response['id'];

    }
}