<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Segments
 * @package LiamProject\Models
 */
class Segments extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'customers/segments';
    }

    /**
     * @param $id
     * @return array|null
     * @throws UnauthorizedException
     */
    public function getEntityById($id): ?array
    {
        $api = "/api/v4/$this->entityName/$id";
        $response =  $this->queryToAmo($api);
        $this->foundedElement = $response;
        return $response;
    }
}