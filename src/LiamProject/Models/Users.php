<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;
use LiamProject\Exceptions\UserNotFoundException;

/**
 * Class Users
 * @package LiamProject\Models
 */
class Users extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'users';
    }

    /**
     * @param $id
     * @return int|null
     * @throws UserNotFoundException
     * @throws UnauthorizedException
     */
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