<?php
namespace LiamProject\Models;


class Segments extends AmocrmEntity
{
    protected function setEntityName(): string
    {
        return 'customers/segments';
    }

    public function getEntityById($id): ?array
    {
        $api = "/api/v4/$this->entityName/$id";
        $response =  $this->queryToAmo($api);
        $this->foundedElement = $response;
        return $response;
    }
}